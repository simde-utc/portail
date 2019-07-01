<?php
/**
 * Gère les utilisateurs.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Traits\Controller\v1\HasUsers;
use App\Traits\Controller\v1\HasImages;
use App\Exceptions\PortailException;

class UserController extends Controller
{
    use HasUsers, HasImages;

    /**
     * Uniqument client: Nécessité de pouvoir gérer les utilisateurs.
     * Uniquement user: Lecture et modification uniquement possible de l'utlisateur.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-get-users'),
            ['only' => 'index']
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-create-users'),
            ['only' => 'store', 'bulkStore']
        );
        $this->middleware(
            \Scopes::matchAnyUser(),
            ['only' => 'show', 'bulkShow']
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-set-info', 'client-edit-users'),
            ['only' => 'update', 'bulkUpdate']
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-manage-users'),
            ['only' => 'destroy', 'bulkDestroy']
        );
    }

    /**
     * Récupère la liste des utilisateurs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $choices = [];

        if (\Scopes::hasOne($request, 'client-get-users-active')) {
            $choices[] = 'active';
        }

        if (\Scopes::hasOne($request, 'client-get-users-inactive')) {
            $choices[] = 'inactive';
        }

        $choices = $this->getChoices($request, $choices);

        if (count($choices) === 2) {
            $users = new User;
        } else {
            $users = User::where('is_active', \Scopes::hasOne($request, 'client-get-users-active'));
        }

        $users = $users->getSelection()->map(function ($user) {
            return $user->hideData();
        });

        return response()->json($users, 200);
    }

    /**
     * Créer un nouvel utilisateur.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $active = $request->input('is_active');

        if ($active) {
            if (!\Scopes::hasOne($request, 'client-get-users-'.($active ? 'active' : 'inactive'))) {
                abort(403, 'Vous n\'avez pas le droit de créer ce type de compte');
            }
        } else {
            $active = \Scopes::hasOne($request, 'client-get-users-active');
        }

        $user = User::create([
            'email' => $request->input('email'),
            'lastname' => strtoupper($request->input('lastname')),
            'firstname' => $request->input('firstname'),
            'is_active' => $active,
        ]);

        // On affecte l'image si tout s'est bien passé.
        $this->setImage($request, $user, 'users/'.$user->id);

        if ($request->filled('details')) {
            if (!\Scopes::hasOne($request, 'client-create-info-details')) {
                abort(403, 'Vous ne pouvez pas créer de détails');
            }

            foreach ($request->input('details') as $key => $value) {
                $user->details()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        if ($request->filled('preferences')) {
            if (!\Scopes::hasOne($request, 'client-create-info-preferences')) {
                abort(403, 'Vous ne pouvez pas créer de préférences');
            }

            foreach ($request->input('preferences') as $key => $value) {
                $user->preferences()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        return response()->json($user->hideSubData(), 201);
    }

    /**
     * Montre un utilisateur.
     *
     * @param UserRequest $request
     * @param string      $user_id
     * @return JsonResponse
     */
    public function show(UserRequest $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id, true);
        $rightsOnUser = is_null($user_id) || (\Auth::id() && $user->id === \Auth::id());

        if ($rightsOnUser) {
            if (!\Scopes::has($request, 'user-get-info-identity-email')) {
                $user->makeHidden('email');
            }

            if (\Scopes::has($request, 'user-get-info-identity-type')) {
                $user->type = $user->type();
            }

            if ($request->has('types')) {
                $possibleTypes = $request->input('types');
                $types = [];

                if ($possibleTypes === '*') {
                    if (!\Scopes::has($request, 'user-get-info-identity-type')) {
                        abort(403, 'Vous n\'avez pas le droit d\'avoir accès aux types de l\'utilisateur');
                    }

                    $possibleTypes = $user->getTypes();
                } else {
                    $possibleTypes = explode(',', $possibleTypes);
                }

                foreach ($possibleTypes as $type) {
                    try {
                        if (!\Scopes::has($request, 'user-get-info-identity-type-'.$type)) {
                            continue;
                        }

                        $method = 'is'.ucfirst($type);

                        if (method_exists($user, $method) && $user->$method()) {
                            $types[$type] = true;
                        } else {
                            $types[$type] = false;
                        }
                    } catch (PortailException $e) {
                        abort(400, 'Le type '.$type.' n\'existe pas !');
                    }
                }

                $user->types = $types;
            }

            if (!\Scopes::has($request, 'user-get-info-identity-timestamps')) {
                $user->makeHidden('last_login_at')->makeHidden('created_at')->makeHidden('updated_at');
            }
        } else {
            $user = $user->hideSubData();
        }

        // Par défaut, on retourne au moins l'id de la personne et son nom.
        return response()->json($user);
    }

    /**
     * Met à jour un utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function update(Request $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);

        $user->email = $request->input('email', $user->email);
        $user->lastname = $request->input('lastname', $user->lastname);
        $user->firstname = $request->input('firstname', $user->firstname);
        $user->is_active = $request->input('is_active', $user->is_active);
        $user->save();

        // On affecte l'image si tout s'est bien passé.
        $this->setImage($request, $user, 'users/'.$user->id);

        return response()->json($user, 200);
    }

    /**
     * (Non géré) Supprime un utilisateur.
     * TODO RGPD.
     *
     * @param Request $request
     * @param string  $user_id
     * @return void
     */
    public function destroy(Request $request, string $user_id)
    {
        abort(403, "Wow l'ami, patience, c'est galère ça...");
    }
}

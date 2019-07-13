<?php
/**
 * Manages users.
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
use App\Traits\Controller\v1\{
	HasUsers, HasImages, HasUserBulkMethods
};
use App\Exceptions\PortailException;

class UserController extends Controller
{
    use HasUserBulkMethods, HasUsers, HasImages;

    /**
     * Only clients: Must have the right to manage users.
     * Oly user: Reading and update only possible by the user.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-get-users'),
            ['only' => ['all', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-create-users'),
            ['only' => ['create']]
        );
        $this->middleware(
        \Scopes::matchAnyClient(),
            ['only' => ['bulkShow']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-set-info', 'client-edit-users'),
            ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-manage-users'),
            ['only' => ['remove']]
        );
    }

    /**
     * Retrieves the user list.
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
     * Creates a new user.
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

        // If everything went fine, affecting image.
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
     * Shows a user.
     *
     * @param UserRequest $request
     * @param string      $user_id
     * @return JsonResponse
     */
    public function show(UserRequest $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);

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

        // By default, at least the user id and name is returned.
        return response()->json($user);
    }

    /**
     * Updates a user.
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

        // If everything went fine, affecting image.
        $this->setImage($request, $user, 'users/'.$user->id);

        return response()->json($user, 200);
    }

    /**
     * Not handled: deletes a user.
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

<?php
/**
 * Gère les préférences utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserPreferenceRequest;
use App\Models\User;
use App\Models\UserPreference;
use App\Traits\Controller\v1\HasUsers;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\PortailException;

class PreferenceController extends Controller
{
    use HasUsers;

    /**
     * Nécessité de pouvoir gérer les préférences de l'utilisateur.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-info-preferences'),
            ['only' => ['index', 'show', 'bulkShow']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-info-preferences'),
            ['only' => ['store', 'bulkStore']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-info-preferences'),
            ['only' => ['update', 'bulkUpdate']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-info-preferences'),
            ['only' => ['destroy', 'bulkDestroy']]
        );
    }

    /**
     * Récupère la liste des préférences d'un utilisateur.
     *
     * @param Request $request
     * @param User    $user
     * @param string  $verb
     * @return Builder
     */
    protected function getPreferences(Request $request, User $user, string $verb)
    {
        $choices = $this->getChoices($request, ['global', 'asso', 'client']);
        $type = \Scopes::getTokenType($request);
        $client = \Scopes::getClient($request);

        if (in_array('asso', $choices)) {
            if (!\Scopes::has($request, $type.'-'.$verb.'-info-preferences-asso')) {
                abort(403, 'Vous n\'avez pas les droits sur les préférences de l\'association');
            }

            $choices[array_search('asso', $choices)] = 'asso-'.$client->asso_id;
        }

        if (in_array('client', $choices)) {
            if (!\Scopes::has($request, $type.'-'.$verb.'-info-preferences-client')) {
                abort(403, 'Vous n\'avez pas les droits sur les préférences du client');
            }

            $choices[array_search('client', $choices)] = 'client-'.$client->id;
        }

        if (in_array('global', $choices)) {
            if (!\Scopes::has($request, $type.'-'.$verb.'-info-preferences-global')) {
                abort(403, 'Vous n\'avez pas les droits sur les préférences globales de l\'utilisateur');
            }
        }

        return $user->preferences()->whereIn('only_for', $choices);
    }

    /**
     * Liste les préférences d'un utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);
        $groups = $this->getPreferences($request, $user, 'get')->get()->groupBy('only_for');
        $array = [];

        foreach ($groups as $only_for => $preferences) {
            $array[$only_for] = [];

            foreach ($preferences as $preference) {
                $array[$only_for] = array_merge($array[$only_for], $preference->toArray());
            }
        }

        if (count($array) === 1) {
            $array = array_values($array)[0];
        }

        return response()->json($array);
    }

    /**
     * Créer une préférence pour un utilisateur.
     *
     * @param UserPreferenceRequest $request
     * @param string                $user_id
     * @return JsonResponse
     */
    public function store(UserPreferenceRequest $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);
        $inputs = $request->input();
        $token = \Scopes::getToken($request);

        // On ajoute l'id associé.
        if ($request->input('only_for') === 'asso') {
            if (!\Scopes::has($request, 'user-create-info-preferences-asso')) {
                abort(403, 'Vous n\'avez pas les droits sur les préférences de l\'association');
            }

            $inputs['only_for'] .= '_'.$token->client->asso_id;
        } else if ($request->input('only_for') === 'client') {
            if (!\Scopes::has($request, 'user-create-info-preferences-client')) {
                abort(403, 'Vous n\'avez pas les droits sur les préférences du client');
            }

            $inputs['only_for'] .= '_'.$token->client->id;
        } else if ($request->input('only_for', 'global') === 'global') {
            if (!\Scopes::has($request, 'user-create-info-preferences-global')) {
                abort(403, 'Vous n\'avez pas les droits sur les préférences globale de l\'utilisateur');
            }
        } else {
            abort(400, 'only_for peut seulement être asso, client ou global');
        }

        if (\Scopes::isUserToken($request)) {
            $preference = UserPreference::create(array_merge(
                $inputs,
                ['user_id' => $user->id]
            ));

            return response()->json($preference, 201);
        }
    }

    /**
     * Montre une préférence d'un utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $key
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $key) = [$key, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $preference = $this->getPreferences($request, $user, 'get')->key($key);

        if ($preference) {
            return response()->json($preference->toArray());
        } else {
            abort(404, 'Cette personne ne possède pas cette préférence');
        }
    }

    /**
     * Met à jour une préférence d'un utilisateur.
     *
     * @param UserPreferenceRequest $request
     * @param string                $user_id
     * @param string                $key
     * @return JsonResponse
     */
    public function update(UserPreferenceRequest $request, string $user_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $key) = [$key, $user_id];
        }

        $user = $this->getUser($request, $user_id);

        if (\Scopes::isUserToken($request)) {
            try {
                $preference = $this->getPreferences($request, $user, 'edit')->key($key);
                $preference->value = $request->input('value', $preference->value);

                if ($preference->update()) {
                    return response()->json($preference);
                } else {
                    abort(503, 'Erreur lors de la modification');
                }
            } catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce préférence, ou il ne peut être modifié');
            }
        }
    }

    /**
     * Supprime une préférence d'un utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $key
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $key) = [$key, $user_id];
        }

        $user = $this->getUser($request, $user_id);

        if (\Scopes::isUserToken($request)) {
            $preference = $this->getPreferences($request, $user, 'manage')->key($key);

            try {
                if ($preference->delete()) {
                    abort(204);
                } else {
                    abort(503, 'Erreur lors de la suppression');
                }
            } catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être supprimé');
            }
        }
    }
}

<?php
/**
 * Gère les systèmes d'authentification de l'utilisateur.
 * TODO: Transformer les get en Trait.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Models\Model;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserAuthRequest;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasUsers;

class AuthController extends Controller
{
    use HasUsers;

    /**
     * Nécessité de pouvoir gérer les systèmes d'authentification de l'utlisateur.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-info-identity-auth', 'client-get-info-identity-auth'),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-info-identity-auth', 'client-create-info-identity-auth'),
            ['only' => ['create']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-set-info-identity-auth', 'client-set-info-identity-auth'),
            ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-info-identity-auth', 'client-manage-info-identity-auth'),
            ['only' => ['remove']]
        );
    }

    /**
     * Liste les systèmes d'authentification de l'utilisateur.
     *
     * @param  Request $request
     * @param  string 	$user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);
        $providers = config('auth.services');
        $result = [];

        foreach ($providers as $name => $provider) {
            if (\Scopes::has($request, \Scopes::getTokenType($request).'-get-info-identity-auth-'.$name)) {
                $result[$name] = $user->$name;
            }
        }

        return response()->json($result);
    }

    /**
     * Ajoute un système d'authentification de l'utilisateur.
     *
     * @param  UserAuthRequest $request
     * @param  string          $user_id
     * @return JsonResponse
     */
    public function store(UserAuthRequest $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);
        $name = $request->input('name');
        $provider = config('auth.services.'.$name);
        $result = [];

        if ($provider === null) {
            abort(404, 'Mauvais nom de service founi');
        } else {
            if (!\Scopes::has($request, \Scopes::getTokenType($request).'-create-info-identity-auth-'.$name)) {
                abort(503, 'Non autorisé');
            }

            $class = resolve($provider['class']);

            if ($class) {
                $auth = $class->addAuth($user->id, $request->input('data'));

                if ($auth instanceof Model) {
                    return response()->json($auth);
                } else {
                    abort(400, 'Il n\'a pas été possible de créer le système d\'authentication '.$name);
                }
            } else {
                abort(404, 'Le service '.$name.' ne permet pas à l\'utlisateur de se connecter');
            }
        }
    }

    /**
     * Montre un système d'authentification de l'utilisateur.
     *
     * @param  Request $request
     * @param  string 	$user_id
     * @param  string 	$name
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $name=null)
    {
        if (is_null($name)) {
            list($user_id, $name) = [$name, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $provider = config('auth.services.'.$name);
        $result = [];

        if ($provider === null) {
            abort(404, 'Mauvais nom de service founi');
        } else {
            if (!\Scopes::has($request, \Scopes::getTokenType($request).'-get-info-identity-auth-'.$name)) {
                abort(503, 'Non autorisé');
            }

            $auth = $user->$name;

            if ($auth) {
                return response()->json($auth);
            }

            abort(404, 'Le service '.$name.' ne permet pas à l\'utlisateur de se connecter');
        }
    }

    /**
     * Il est impossible de modifier un système d'authentification actuellement.
     * TODO
     *
     * @param  Request $request
     * @param  string 	$user_id
     * @param  string 	$name
     * @return void
     */
    public function update(Request $request, string $user_id, string $name=null)
    {
        abort(405, 'Il n\'est pas possible de modifier un système d\'authentification');
    }

    /**
     * Supprime un système d'authentification de l'utilisateur.
     *
     * @param  Request $request
     * @param  string 	$user_id
     * @param  string 	$name
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $name=null)
    {
        if (is_null($name)) {
            list($user_id, $name) = [$name, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $provider = config('auth.services.'.$name);
        $result = [];

        if ($provider === null) {
            abort(404, 'Mauvais nom de service founi');
        } else {
            if (!\Scopes::has($request, \Scopes::getTokenType($request).'-manage-info-identity-auth-'.$name)) {
                abort(503, 'Non autorisé');
            }

            $auth = $user->$name;

            if ($auth) {
                $auth->delete();

                abort(204);
            }

            abort(404, 'Le service '.$name.' ne permet pas à l\'utlisateur de se connecter');
        }
    }
}

<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasUsers;

/**
 * @resource Connected User
 *
 * Affiche des informations sur l'utilisateur connecté
 */
class AuthController extends Controller
{
	use HasUsers;

	public function __construct() {
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-info-identity-auth'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-info-identity-auth'),
			['only' => ['store']]
		);
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-set-info-identity-auth'),
			['only' => ['update']]
		);
		$this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-info-identity-auth'),
			['only' => ['destroy']]
		);
	}

	/**
	 * List User's Providers
	 *
	 * Retourne tous les providers de l'utilisateur connecté
	 * @param  Request $request
	 * @return Json
	 */
	public function index(Request $request, string $user_id = null) {
		$user = $this->getUser($request, $user_id);
		$providers = config('auth.services');
		$result = [];

		foreach ($providers as $name => $provider) {
			if (\Scopes::has($request, 'user-get-info-identity-auth-'.$name))
				$result[$name] = $user->$name;
		}

		// On retourne tous les providers de la personne
		return response()->json($result);
	}

	public function store(Request $request, string $user_id = null) {
		$user = $this->getUser($request, $user_id);
		$name = $request->input('name');
		$provider = config('auth.services.'.$name);
		$result = [];

		if ($provider === null)
			return response()->json(['message' => 'Mauvais nom de service founi'], 400);
		else {
			if (!\Scopes::has($request, 'user-create-info-identity-auth-'.$name))
				return response()->json(['message' => 'Non autorisé'], 503);

			$class = resolve($provider['class']);

			if ($class)
				return response()->json($class->addAuth($user->id, $request->input('data')));
			else
				return response()->json(['message' => 'Le service '.$name.' ne permet pas à l\'utlisateur de se connecter'], 404);
		}
	}

	/**
	 * Get User Provider
	 *
	 * Retourne le provider de la personne
	 * @param  Request $request
	 * @param  string $name
	 * @return Json
	 */
	public function show(Request $request, string $user_id, string $name = null) {
        if (is_null($name))
            list($user_id, $name) = [$name, $user_id];

		$user = $this->getUser($request, $user_id);
		$provider = config('auth.services.'.$name);
		$result = [];

		if ($provider === null)
			return response()->json(['message' => 'Mauvais nom de service founi'], 400);
		else {
			if (!\Scopes::has($request, 'user-get-info-identity-auth-'.$name))
				return response()->json(['message' => 'Non autorisé'], 503);

			$model = resolve($provider['model']);

			if ($model) {
				$auth = $model->find($user->id);

				if ($auth)
					return response()->json($auth);
			}

			return response()->json(['message' => 'Le service '.$name.' ne permet pas à l\'utlisateur de se connecter'], 404);
		}
	}

	public function destroy(Request $request, string $user_id, string $name = null) {
        if (is_null($name))
            list($user_id, $name) = [$name, $user_id];

		$user = $this->getUser($request, $user_id);
		$provider = config('auth.services.'.$name);
		$result = [];

		if ($provider === null)
			return response()->json(['message' => 'Mauvais nom de service founi'], 400);
		else {
			if (!\Scopes::has($request, 'user-manage-info-identity-auth-'.$name))
				return response()->json(['message' => 'Non autorisé'], 503);

			$model = resolve($provider['model']);

			if ($model) {
				$auth = $model->find($user->id);

				if ($auth) {
					if ($auth->delete())
						return abort(204);
					else
						return abort(500, 'Erreur lors de la suppression');
				}
			}

			return response()->json(['message' => 'Le service '.$name.' ne peut pas être supprimé car elle ne permet pas à l\'utlisateur de se connecter'], 404);
		}
	}
}

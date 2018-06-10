<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\PortailException;

/**
 * @resource Connected User
 *
 * Affiche des informations sur l'utilisateur connecté
 */
class UserAuthController extends Controller
{
	public function __construct() {
		$this->middleware(
			\Scopes::matchAnyUser()
		);
	}

	/**
	 * List User's Providers
	 *
	 * Retourne tous les providers de l'utilisateur connecté
	 * @param  Request $request
	 * @return Json
	 */
	public function index(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);
		$providers = config('auth.services');
		$result = [];

		foreach ($providers as $name => $provider) {
			$model = resolve($provider['model']);

			if ($model !== null && \Scopes::has($request, 'user-get-info-identity-auth-'.$name)) {
				$data = $model->find($user->id);

				if ($data !== null) {
					$result[$name] = $data;
				}
			}
		}

		// On retourne tous les providers de la personne
		return $result;
	}

	/**
	 * Get User Provider
	 *
	 * Retourne le provider de la personne
	 * @param  Request $request
	 * @param  string $name
	 * @return Json
	 */
	public function show(Request $request, $user_id, $name = null) {
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

			if ($model !== null) {
				$data = $model->find($user->id);

				if ($data !== null)
					return $data;
			}

			return response()->json(['message' => 'Le service '.$name.' ne permet pas à l\'utlisateur de se connecter'], 404);
		}
	}
}

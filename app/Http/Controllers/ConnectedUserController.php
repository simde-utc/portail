<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @resource Connected User
 *
 * Affiche des informations sur l'utilisateur connecté
 */
class ConnectedUserController extends Controller
{
	/**
	 * Show Connected User
	 *
	 * Renvoie des informations sur l'utilisateur connecté
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function index(Request $request): JsonResponse {
		$user = $request->user();

		if (!\Scopes::has($request, 'user-get-info-identity-emails-main'))
			$user->makeHidden('email');

		if (\Scopes::has($request, 'user-get-info-identity-type'))
			//$user->type = \App\Services\Visible\Visible::getType($user->id); TODO

			if (!\Scopes::has($request, 'user-get-info-identity-timestamps'))
				$user->makeHidden('last_login_at')->makeHidden('created_at')->makeHidden('updated_at');

		// Par défaut, on retourne au moins l'id de la personne
		return response()->json($user);
	}

	/**
	 * List User's Providers
	 *
	 * Retourne tous les providers de l'utilisateur connecté
	 * @param  Request $request
	 * @return array
	 */
	public function getProviders(Request $request): array {
		$user = $request->user();
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
	 * @return JsonResponse
	 */
	public function getProvider(Request $request, string $name): JsonResponse {
		$user = $request->user();
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

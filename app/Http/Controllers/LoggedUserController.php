<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @resource Connected User
 *
 * Affiche des informations sur l'utilisateur connecté
 */
class LoggedUserController extends Controller
{
	/**
	 * Show Connected User
	 *
	 * Renvoie des informations sur l'utilisateur connecté
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$user = $request->user();

		if (!\Scopes::has($request, 'user-get-info-identity-emails-main'))
			$user->makeHidden('email');

		if (\Scopes::has($request, 'user-get-info-identity-type'))
			$user->type = $user->type();

		if ($request->has('withTypes')) {
			foreach (explode(',', $request->input('withTypes')) as $type) {
				try {
					if (!\Scopes::has($request, 'user-get-info-identity-type-'.$type))
						continue;

					$method = 'is'.ucfirst($type);
					$type = 'is_'.$type;

					if (method_exists($user, $method) && $user->$method())
						$user->$type = true;
					else
						$user->$type = false;
				} catch (\Exception $e) {
					abort(400, 'Le type '.$type.' n\'existe pas !');
				}
			}
		}

		if (!\Scopes::has($request, 'user-get-info-identity-timestamps'))
			$user->makeHidden('last_login_at')->makeHidden('created_at')->makeHidden('updated_at');

		if ($request->has('allDetails'))
			$user->details = UserDetail::allToArray($user->id);
		else if ($request->filled('withDetails')) {
			$details = [];

			foreach (explode(',', $request->input('withDetails')) as $input)
				$details[$input] = UserDetail::$input($user->id);

			$user->details = $details;
		}

		// Par défaut, on retourne au moins l'id de la personne et son nom
		return $user;
	}

	/**
	 * List User's Providers
	 *
	 * Retourne tous les providers de l'utilisateur connecté
	 * @param  Request $request
	 * @return Json
	 */
	public function getProviders(Request $request) {
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
	 * @return Json
	 */
	public function getProvider(Request $request, string $name) {
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

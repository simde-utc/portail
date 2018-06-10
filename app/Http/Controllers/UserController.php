<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @resource User
 *
 * Gestion des utilisateurs
 */
class UserController extends Controller
{
	public function __construct() {
		$this->middleware(
			\Scopes::matchAnyClient(),
			['only' => 'index']
		);
		$this->middleware(
			\Scopes::matchAnyUserOrClient()
		);
	}

	/**
	 * List Users
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		return response()->json(User::all(), 200);
	}

	/**
	 * Create User
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id, true);
		$rightsOnUser = is_null($user_id) || (\Auth::id() && $user->id === \Auth::id());

		if ($rightsOnUser) {
			if (!\Scopes::has($request, 'user-get-info-identity-email'))
				$user->makeHidden('email');

			if (\Scopes::has($request, 'user-get-info-identity-type'))
				$user->type = $user->type();

			if ($request->has('allTypes')) {
				if (!\Scopes::has($request, 'user-get-info-identity-type'))
					abort(403, 'Vous n\'avez pas le droit d\'avoir accès aux types de l\'utilisateur');

				foreach ($user->types as $type) {
					$method = 'is'.ucfirst($type);
					$type = 'is_'.$type;

					if (method_exists($user, $method) && $user->$method())
						$user->$type = true;
					else
						$user->$type = false;
				}
			}
			else if ($request->has('withTypes')) {
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
					} catch (PortailException $e) {
						abort(400, 'Le type '.$type.' n\'existe pas !');
					}
				}
			}

			if (!\Scopes::has($request, 'user-get-info-identity-timestamps'))
				$user->makeHidden('last_login_at')->makeHidden('created_at')->makeHidden('updated_at');

			if ($request->has('allDetails')) {
				if (!\Scopes::has($request, 'user-get-info-details'))
					abort(403, 'Il est nécessaire soit d\'avoir la permission d\'avoir tous les détails soient de spécifier lesquels voir');

				$user->details = $user->details()->allToArray();
			}
			else if ($request->filled('withDetails')) {
				$details = [];

				foreach (explode(',', $request->input('withDetails')) as $key) {
					try {
						if (!\Scopes::has($request, 'user-get-info-details-'.$key))
							abort(403, 'Vous n\'avez pas le droit d\'avoir accès à cette information');
					} catch (PortailException $e) {
						abort(403, 'Il n\'existe pas de détail utilisateur de ce nom: '.$key);
					}

					try {
						$details[$key] = $user->details()->valueOf($key);
					} catch (PortailException $e) {
						$details[$key] = null;
					}
				}

				$user->details = $details;
			}
		}
		else
			$user = $this->hideUserData($request, $user);

		// Par défaut, on retourne au moins l'id de la personne et son nom
		return $user;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}

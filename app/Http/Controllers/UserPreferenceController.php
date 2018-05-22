<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserPreference;

class UserPreferenceController extends Controller
{
    public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-roles-users']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-set-roles-users']
			),
			['only' => ['store', 'update']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-manage-roles-users']
			),
			['only' => ['destroy']]
		);
    }

	protected function getUser(Request $request, int $user_id = null) {
        if (\Scopes::isClientToken($request))
            $user = User::find($user_id ?? null);
        else {
            $user = \Auth::user();

            if (!is_null($user_id) && $user->id !== $user_id)
                abort(403, 'Il ne vous est pas autorisé d\'accéder aux rôles des autres utilisateurs');
        }

		if ($user)
			return $user;
		else
			abort(404, "Utilisateur non trouvé");
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);

		return response()->json(UserPreference::allToArray($user->id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);

		if ($user) {
			if (method_exists((new UserPreference), $request->input('key')))
				abort(403, "Cette préférence ne peut pas être crée ou modifiée");

			if (\Scopes::isUserToken($request)) {
				UserPreference::create(array_merge(
					$request->input(),
					['user_id' => $user->id]
				));

				return response()->json(UserPreference::find($user->id, $request->input('key'))->toArray(true));
			}
		}
		else
			abort(404, "Utilisateur non trouvé");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $user_id, $key = null) {
        if (is_null($key))
            list($user_id, $key) = [$key, $user_id];

        $user = $this->getUser($request, $user_id);
		$detail = UserPreference::find($user->id, $key);

		if ($detail)
			return response()->json($detail->toArray(true));
		else {
			$detail = UserPreference::$key($user->id);

			if (!is_null($detail))
				return response()->json([
					$key => $detail
				]);
			else
				abort(404, 'Cette personne ne possède pas cette préférence');
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id, $key = null) {
        if (is_null($key))
            list($user_id, $key) = [$key, $user_id];

		$user = $this->getUser($request, $user_id);

		if ($user) {
			if (method_exists((new UserPreference), $key))
				abort(403, "Cette préférence ne peut pas être modifiée ou crée");

			if (\Scopes::isUserToken($request)) {
				$detail = UserPreference::find($user->id, $key);

				if ($detail) {
					$detail->value = $request->input('value', $detail->value);

					if ($detail->update())
						return response()->json(UserPreference::find($user->id, $key)->toArray(true));
					else
						abort(503, 'Erreur lors de la modification');
				}
				else
					abort(404, "Préférence non trouvé");
			}
		}
		else
			abort(404, "Utilisateur non trouvé");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $user_id, $key = null) {
        if (is_null($key))
            list($user_id, $key) = [$key, $user_id];

		$user = $this->getUser($request, $user_id);

		if ($user) {
			if (method_exists((new UserPreference), $key))
				abort(403, "Cette préférence ne peut être supprimée");

			if (\Scopes::isUserToken($request)) {
				$detail = UserPreference::find($user->id, $key);

				if ($detail) {
					if ($detail->delete())
						abort(204);
					else
						abort(503, 'Erreur lors de la suppression');
				}
				else
					abort(404, "Préférence non trouvé");
			}
		}
		else
			abort(404, "Utilisateur non trouvé");
    }
}

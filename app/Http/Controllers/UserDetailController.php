<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserDetail;
use App\Exceptions\PortailException;

class UserDetailController extends Controller
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

        // On affiche chaque détail sous forme clé => valeur
		return response()->json($user->details()->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);

		if (\Scopes::isUserToken($request)) {
			$detail = UserDetail::create(array_merge(
				$request->input(),
				['user_id' => $user->id]
			));

			return response()->json($detail, 201);
        }
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

        try {
            return response()->json($user->details()->toArray($key));
        }
        catch (PortailException $e) {
            abort(404, 'Cette personne ne possède pas ce détail');
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
			if (\Scopes::isUserToken($request)) {
                try {
                    $detail = $user->details()->key($key);
                    $detail->value = $request->input('value', $detail->value);

                    return response()->json($detail);
                }
                catch (PortailException $e) {
                    abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être modifié');
                }
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
			if (\Scopes::isUserToken($request)) {
                try {
                    $detail = $user->details()->where('key', $key);

					if ($detail->delete())
                        abort(204);
					else
						abort(503, 'Erreur lors de la suppression');
                }
                catch (PortailException $e) {
                    abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être supprimé');
                }
			}
		}
		else
			abort(404, "Utilisateur non trouvé");
    }
}

<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDetail;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasUsers;

class DetailController extends Controller
{
    use HasUsers;

    public function __construct(Request $request) {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-info-details'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-info-details'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-info-details'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-info-details'),
            ['only' => ['destroy']]
        );
    }

    protected function checkScope(Request $request, string $key, string $verb) {
        try {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-details-'.$key))
                abort(403, 'Vous n\'avez pas les droits sur cette information');
        } catch (PortailException $e) {
            abort(403, 'Il n\'existe pas de détail utilisateur de ce nom: '.$key);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);

        // On affiche chaque détail sous forme clé => valeur
		return response()->json($user->details()->allToArray());
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

        $this->checkScope($request, $key, 'get');
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

        $this->checkScope($request, $key, 'edit');
		$user = $this->getUser($request, $user_id);

		if (\Scopes::isUserToken($request)) {
            try {
                $detail = $user->details()->key($key);
                $detail->value = $request->input('value', $detail->value);

                if ($detail->update())
                    return response()->json($detail);
                else
                    abort(503, 'Erreur lors de la modification');
            }
            catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être modifié');
            }
		}
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

        $this->checkScope($request, $key, 'manage');
		$user = $this->getUser($request, $user_id);

		if (\Scopes::isUserToken($request)) {
            try {
                $detail = $user->details()->key($key);

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
}

<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Exceptions\PortailException;
use App\Models\Client;
use App\Models\Asso;
use App\Models\Role;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

class ClientController extends Controller
{
    public function index(Request $request) {
		if (\Auth::user()->hasOnePermission('client'))
			return Client::all()->makeVisible('secret');
		else {
			$roles = Role::getRoleAndItsParents('resp info');
			$assos = \Auth::user()->currentJoinedAssos()->wherePivotIn('role_id', $roles->pluck('id'));

			return Client::where('asso_id', $assos->pluck('id'))->getPaginate();
		}
    }

    /**
     * Créer un nouveau client (nécessaire d'être un admin)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
		\Scopes::checkScopesForGrantType($request->scopes ?? [], 'client_credentials');

        return response()->json(Client::create([
			'user_id' => \Auth::id(),
			'asso_id' => $request->asso_id,
			'name' => $request->name,
			'secret' => str_random(40),
			'redirect' => $request->redirect,
			'personal_access_client' => false,
			'password_client' => false,
			'revoked' => false,
			'scopes' => json_encode($request->input('scopes', [])),
		])->makeVisible('secret'), 201);
    }

    public function update(Request $request, $clientId) {
		$client = Client::find($clientId);

		if ($client) {
			\Scopes::checkScopesForGrantType($request->scopes ?? [], 'client_credentials');

			if (isset($request['asso_id']) && $request->asso_id !== $client->asso_id)
				throw new PortailException('Il n\'est pas possible de change l\'association à laquelle ce client est lié');

			return response()->json($client->update([
				'user_id' => \Auth::id(),
				'name' => $request->input('name', $client->name),
				'redirect' => $request->input('redirect', $client->redirect),
				'scopes' => isset($request['scopes']) ? json_encode($request->input('scopes', [])) : $client->scopes,
			]), 200);
		}
		else
			abort(404, 'Le client n\'a pas été trouvé');
    }

    /**
     * Delete the given client.
     *
     * @param  Request  $request
     * @param  string  $clientId
     * @return Response
     */
    public function destroy(Request $request, $clientId) {
		$client = Client::find($clientId);

		if ($client) {
			$client->delete();

			abort(204);
		}
		else
			abort(404, 'Le client n\'a pas été trouvé');
    }
}

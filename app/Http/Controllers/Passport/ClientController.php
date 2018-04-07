<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

class ClientController extends \Laravel\Passport\Http\Controllers\ClientController
{
    public function forUser(Request $request)
    {
		// Afficher en fonction de l'asso
        $userId = $request->user()->getKey();

        return $this->clients->activeForUser($userId)->makeVisible('secret');
    }

    /**
     * Store a new client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation->make($request->all(), [
			'asso_id' => 'required',
            'name' => 'required|max:255',
            'redirect' => 'required|url',
        ])->validate();

        return Client::create([
			'user_id' => \Auth::id(),
			'asso_id' => $request->asso_id,
			'name' => $request->name,
			'secret' => str_random(40),
			'redirect' => $request->redirect,
			'personal_access_client' => false,
			'password_client' => false,
			'revoked' => false,
		])->makeVisible('secret');
    }

    public function update(Request $request, $clientId)
    {
		return response()->json(['message' => 'La modification n\'est pas permise'], 403);
		// On ne permet pas la modification après création pour éviter un changement volontaire
    }

    /**
     * Delete the given client.
     *
     * @param  Request  $request
     * @param  string  $clientId
     * @return Response
     */
    public function destroy(Request $request, $clientId)
    {
		// Regarder si on a le droit
        $client = $this->clients->findForUser($clientId, $request->user()->getKey());

        if (! $client) {
            return new Response('', 404);
        }

        $this->clients->delete(
            $client
        );
    }
}

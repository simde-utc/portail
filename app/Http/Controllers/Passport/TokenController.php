<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;

class TokenController extends Controller
{
	public function link (Request $request) {
		// On vérifie que l'utilisateur lié au token est toujours connecté
		if ($request->user() !== null) {
			$token = $request->user()->token();
			$client = Client::find($token->client_id);

			// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session
			if ($client !== null && !$client->personal_access_client && !$client->password_client)
				return redirect($client['redirect'].'?session='.encrypt(\Session::getId())); // Cette session doit être mise dans le header Session
			else
				return response()->json(['message' => 'Il est inutile de lier le token à une session'], 400);
		}
	}
}

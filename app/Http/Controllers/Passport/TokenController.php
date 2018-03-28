<?php

namespace App\Http\Controllers\Passport;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use Laravel\Passport\Token;

class TokenController extends Controller
{
	public function create (Request $request) {
		// On vérifie que l'utilisateur lié au token est toujours connecté
		if ($request->user() !== null) {
			$token = $request->user()->token();
			$client = Client::find($token->client_id);

			// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session
			if ($client !== null && !$client->personal_access_client && !$client->password_client)
				return response()->json(['code' => encrypt($token->id)], 200); // Cette session doit être mise dans le header Session
		}

		return response()->json(['message' => 'Il est inutile de lier le token à une session'], 400);
	}

	public function link (Request $request) {
		// On vérifie que l'utilisateur lié au token est toujours connecté
		if ($request->input('code') === null)
			return response()->json(['message' => 'Il est nécessaire de fournir un code pour récupérer la session'], 400);

		try {
			$token_id = decrypt($request->input('code'));
			$token = Token::find($token_id);
			$client = Client::find($token->client_id);
		}
		catch (\Exception $e) {
			return response()->json(['message' => 'Ce code de token est invalide'], 503);
		}

		if ($token->user_id != \Auth::id())
			return response()->json(['message' => 'Le token n\'est pas lié à cet utilisateur'], 503);

		// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session
		if ($client !== null && !$client->personal_access_client && !$client->password_client)
			return redirect($client['redirect'].'?session='.encrypt(\Session::getId())); // Cette session doit être mise dans le header Session
		else
			return response()->json(['message' => 'Il est inutile de lier le token à une session'], 400);
	}
}

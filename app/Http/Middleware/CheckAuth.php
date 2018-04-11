<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Session;
use App\Models\Passport\TokenSession;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;
use Illuminate\Contracts\Encryption\DecryptException;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	 public function handle(\Illuminate\Http\Request $request, Closure $next)
	 {
		// On force le retour en JSON:
		$request->headers->set('Accept', 'application/json');

		// On vérifie que l'utilisateur lié au token est toujours connecté
		if ($request->user() !== null) {
			$token = $request->user()->token();
			$client = Client::find($token->client_id);

			// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session
			if ($client !== null && !$client->personal_access_client && !$client->password_client) {
				$session = Session::find($token->session_id);

				if ($session === null)
					return response()->json(['message' => 'La session est invalide ou a expiré'], 403);
				elseif ($session->user_id !== $request->user()->id)
					return response()->json(['message' => 'L\'utilisateur n\'est plus connecté'], 410);
			}
		}

		return $next($request);
    }
}

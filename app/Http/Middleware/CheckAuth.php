<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Session;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;

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
		// On vérifie que l'utilisateur lié au token est toujours connecté
		if ($request->user() !== null) {
			$token = $request->user()->token();
			$client = Client::find($token->client_id);

			// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou un token lié à une application sans session
			if ($client !== null && !$client->personal_access_client && !$client->password_client) {
				$session = Session::find($token->session_id);

				if ($session === null) {
					// Si la session n'existe plus/pas, on dévalide le token
					$token->revoked = true;
					$oken->timestamps = false;
					$token->save();

					return response()->json(['message' => 'Le token n\'est plus valide'], 410);
				}
				elseif ($session->user_id !== $request->user()->id)
					return response()->json(['message' => 'L\'utilisateur n\'est plus connecté'], 410);
			}
		}

		return $next($request);
    }
}

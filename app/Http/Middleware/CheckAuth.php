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
		// On vérifie que l'utilisateur lié au token est toujours connecté
		if ($request->user() !== null) {
			$token = $request->user()->token();
			$client = Client::find($token->client_id);

			// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session
			if ($client !== null && !$client->personal_access_client && !$client->password_client) {
				if ($request->header('session') === null)
					return response()->json(['message' => 'Ce token doit être lié à une session'], 400);

				try {
					$session_id = decrypt($request->header('session'));
				}
				catch (\Exception $e) {
					return response()->json(['message' => 'Cet identifiant de session est invalide'], 503);
				}

				$session = Session::find($session_id);
				// On vérifie que l'utilisateur est toujours connecté et qu'il s'agit toujours du même
				if ($session === null)
					return response()->json(['message' => 'La session est invalide ou a expiré'], 503);
				elseif ($session->user_id !== $request->user()->id)
					return response()->json(['message' => 'L\'utilisateur n\'est plus connecté'], 410);
			}
		}

		return $next($request);
    }
}

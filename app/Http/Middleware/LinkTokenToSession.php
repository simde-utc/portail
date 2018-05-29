<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;
use App\Models\Session;

class LinkTokenToSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$response = $next($request);

		if ($response->status() === 200 && isset(json_decode($response->content(), true)['access_token'])) {
			$tokenData = json_decode(base64_decode(explode('.', json_decode($response->content(), true)['access_token'])[0]), true);

			if ($tokenData !== null) {
				$token = Token::find($tokenData['jti']);

				if ($token !== null) {
					$client = Client::find($token->client_id);

					// On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session
					if ($client !== null && !$client->personal_access_client && !$client->password_client) {
						$authCode = AuthCode::where('user_id', $token->user_id)->where('client_id', $token->client_id)->where('scopes', json_encode($token->scopes))->where('revoked', true)->orderBy('expires_at', 'DESC')->first();

						// On ne procède uniquement sur les tokens liés à une session
						if ($authCode != null) {
							$token->session_id = $authCode->session_id;
							$token->timestamps = false;
							$token->save();

							// On supprime les duplications du trio client/user/scopes qui ne sont plus utilisées ou sont expirées
							Token::where('user_id', $authCode->user_id)->where('client_id', $authCode->client_id)->where('user_id', $authCode->user_id)->where('id', '!=', $token->id)->get()->each(function ($tokenToDelete, $key) use ($authCode) {
								$session = Session::find($tokenToDelete->session_id);
								// Si la session a expiré ou que l'utlisateur n'est plus connecté, on supprime le token en vue d'un nouveau
								if ($session === null || $tokenToDelete->session_id === $authCode->session_id)
									$tokenToDelete->delete();
							});
						}
					}
				}
			}
		}
		elseif ($response->status() !== 200 && $response->status() !== 400 && $request->user() !== null) {
			$authCode = AuthCode::where('user_id', $request->user()->id)->where('client_id', $request->input('client_id'))->whereNull('session_id')->orderBy('expires_at', 'DESC')->first();

			if ($authCode !== null) {
				$authCode->session_id = \Session::getId();
				$authCode->timestamps = false;
				$authCode->save();
			}
		}

		return $response;
    }
}

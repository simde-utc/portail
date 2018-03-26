<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\Token;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;

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

					if ($client !== null && !$client->personal_access_client && !$client->password_client) {
						$authCode = AuthCode::where('user_id', $token->user_id)->where('client_id', $token->client_id)->where('scopes', json_encode($token->scopes))->where('revoked', true)->orderBy('expires_at', 'DESC')->first();

						// Pour résoudre une faille de sécurité de notre super Laravel/Passport, à la génération d'un nouveau code, il faut révoker toutes les anciennes du même type
						Token::where('user_id', $authCode->user_id)->where('client_id', $authCode->client_id)->where('scopes', $authCode->scopes)->where('session_id', $authCode->session_id)->update(['revoked' => true]);

						$token->session_id = $authCode->session_id;
						$token->timestamps = false;
						$token->save();
					}
				}
			}
		}
		elseif ($response->status() !== 200 && $response->status() !== 400) {
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

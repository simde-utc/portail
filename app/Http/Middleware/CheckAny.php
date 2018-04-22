<?php

namespace App\Http\Middleware;

use Closure;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use League\OAuth2\Server\Exception\OAuthServerException;

class CheckAny
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle(\Illuminate\Http\Request $request, Closure $next, ...$args) {
		// On vérifie que le token n'est pas lié à un utilisateur
		if ($request->bearerToken() !== null) {
			$bearerToken = $request->bearerToken();
			$tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
			$token = Token::find($tokenId);

			if ($token !== null && $token->user_id === null) {
				unset($args[1]);
				return app(\App\Http\Middleware\CheckClient::class)->handle($request, $next, ...$args);
			}
		}

		unset($args[2]);
		return app(\App\Http\Middleware\CheckUser::class)->handle($request, $next, ...$args);
	}
}

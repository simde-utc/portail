<?php

namespace App\Http\Middleware;

use Closure;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use League\OAuth2\Server\Exception\OAuthServerException;

class CheckClient
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
		// On vérifie que le token n'est pas lié à un utilisateur
		$bearerToken = $request->bearerToken();
		$tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
		$user_id = Token::find($tokenId)->user_id;

		if ($user_id !== null)
			throw OAuthServerException::accessDenied();

		return $next($request);
    }
}

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
	 public function handle(\Illuminate\Http\Request $request, Closure $next)
	 {
		// On vérifie que le token n'est pas lié à un utilisateur
		$bearerToken = $request->bearerToken();

		if ($bearerToken === null)
			return $this->checkUser($request, $next);

		$tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
		$token = Token::find($tokenId);

		if ($token === null || $token->user_id !== null)
			return $this->checkUser($request, $next);

		return $next($request);
    }

	private function checkUser(\Illuminate\Http\Request $request, Closure $next) {
		return app(\Illuminate\Auth\Middleware\Authenticate::class)->handle($request, $next, 'api');
	}
}

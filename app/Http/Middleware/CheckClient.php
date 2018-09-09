<?php

namespace App\Http\Middleware;

use Closure;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

class CheckClient
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
		 $bearerToken = $request->bearerToken();
		 $tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
		 $token = Token::find($tokenId);

		 if ($token === null || $token->user_id !== null)
			 throw new AuthenticationException;

 		if (count($args) > 0) {
			if ($args[0] == 0) {
	 			$scopes = [];

				foreach (explode('|', $args[1]) as $scope)
					array_push($scopes, \Scopes::getMatchingScopes(explode('|', $scope), 'user'));
	 		}
	 		else
				$scopes = [\Scopes::getMatchingScopes(explode('|', $args[1]), 'client')];

			return app(CheckClientCredentials::class)->handle($request, function ($request) use ($next, $scopes, $token) {
				if ($token->transient())
					throw new AuthenticationException;

				$tokenScopes = $token->scopes;

				// On vérifie pour chaque ensemble de scopes
				foreach ($scopes as $scopeList) {
					// Qu'on en possède au moins un parmi la liste
					if (empty(array_intersect($tokenScopes, $scopeList)))
						throw new MissingScopeException($scopes);
				}

				return $next($request);
			});
 		}
 		else
 			return app(CheckClientCredentials::class)->handle($request, $next);
    }
}

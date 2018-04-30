<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;

class CheckUser
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle(\Illuminate\Http\Request $request, Closure $next, ...$args) {
		if (count($args) > 0) {
			if ($args[0] == 0) {
				$scopes = [];

				foreach (explode('|', $args[1]) as $scope)
					array_push($scopes, \Scopes::getMatchingScopes(explode('|', $scope), 'user'));
			}
			else
				$scopes = [\Scopes::getMatchingScopes(explode('|', $args[1]), 'user')];

			return app(Authenticate::class)->handle($request, function ($request) use ($next, $scopes) {
				if (!$request->user() || !$request->user()->token())
	            	throw new AuthenticationException;

				$tokenScopes = $request->user()->token()->scopes;
				// On vérifie pour chaque ensemble de scopes
		        foreach ($scopes as $scopeList) {
					// Qu'on en possède au moins un parmi la liste
		            if (empty(array_intersect($tokenScopes, $scopeList)))
						throw new MissingScopeException($scopes);
		        }

				return $next($request);
			}, 'api');
		}
		else
			return app(Authenticate::class)->handle($request, $next, 'api');
	}
}

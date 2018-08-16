<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class UserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	public function handle($request, Closure $next, ...$args)
	{
		if (count($args) === 0)
			throw new \Exception('Il est nécessaire de spécifier au moins un rôle');

		if (\Auth::user()) {
			foreach ($args as $role) {
				if ($wrongIfEqual = ($role[0] === '!'))
					$role = substr($role, 1);

				if (\Auth::user()->hasOneRole($role) !== $wrongIfEqual)
					return $next($request);
			}

			throw new AuthorizationException('L\'utilisateur ne possède aucun rôle: '.implode(', ', $args));
		}

		return $next($request);
 	}
}

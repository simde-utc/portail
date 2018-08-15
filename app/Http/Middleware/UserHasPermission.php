<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class UserHasPermission
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
			throw new \Exception('Il est nécessaire de spécifier au moins un permission');

		if (\Auth::user()) {
			foreach ($args as $permission) {
				if ($wrongIfEqual = ($permission[0] === '!'))
					$permission = substr($permission, 1);

				if (\Auth::user()->hasOnePermission($permission) !== $wrongIfEqual)
					return $next($request);
			}

			throw new AuthorizationException('L\'utilisateur ne possède aucune permission: '.implode(', ', $args));
		}

		return $next($request);
 	}
}

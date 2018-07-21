<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class UserIs
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
			throw new \Exception('Il est nécessaire de spécifier au moins un type');

		if (\Auth::id()) {
			foreach ($args as $type) {
				if ($wrongIfEqual = ($type[0] === '!'))
					$type = substr($type, 1);

				$method = 'is'.ucfirst($type);

				if (!method_exists(\Auth::user(), $method))
					throw new \Exception('Le type '.$type.' n\'existe pas !');

				if (\Auth::user()->$method() !== $wrongIfEqual)
					return $next($request);
			}
		}

		throw new AuthorizationException('L\'utilisateur n\'est d\'aucun type: '.implode(', ', $args));
 	}
}

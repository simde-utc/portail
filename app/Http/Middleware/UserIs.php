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
		if (\Auth::user()) {
			foreach ($args as $type) {
				if ($wrongIfEqual = ($type[0] === '!'))
					$type = substr($type, 1);

				$method = 'is'.ucfirst($type);

				if (!method_exists(\Auth::user(), $method))
					throw new \Exception('Le type '.$type.' n\'existe pas !');

				if (\Auth::user()->$method() === $wrongIfEqual)
					throw new AuthorizationException('L\'utilisateur n\'est pas de ce type: '.$type);
			}

			return $next($request);
		}
		else
    		throw new AuthorizationException('L\'utilisateur n\'est pas connecté');
 	}
}

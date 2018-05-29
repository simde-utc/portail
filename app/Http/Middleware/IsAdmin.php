<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class IsAdmin
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
		if (\Auth::user() && \Auth::user()->hasOneRole('admin'))
			return $next($request);

    	throw new AuthorizationException('Il est nécessaire d\'être un administrateur');
 	}
}

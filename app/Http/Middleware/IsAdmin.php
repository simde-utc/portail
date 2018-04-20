<?php

namespace App\Http\Middleware;

use Closure;

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

    	return abort('Vous n\'êtes pas autorisé', 403);
 	}
}

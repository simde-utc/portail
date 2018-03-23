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
		echo 'a';
		if (\Auth::user() && \Auth::id() === 2)
			return $next($request);

    	return redirect('/home');
 	}
}

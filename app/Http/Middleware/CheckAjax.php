<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class CheckAjax
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	 public function handle(\Illuminate\Http\Request $request, Closure $next) {
		 if (!$request->ajax())
		 	throw new AuthenticationException;

        return $next($request);
    }
}

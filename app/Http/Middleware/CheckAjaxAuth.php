<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class CheckAjaxAuth
{
	/**
	* Handle an incoming request.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  \Closure  $next
	* @return mixed
	*/
	public function handle(\Illuminate\Http\Request $request, Closure $next, ...$args) {
		try {
			return app(\App\Http\Middleware\CheckAuth::class)->handle($request, $next, ...$args);
		} catch (AuthenticationException $e) {
			return app(\App\Http\Middleware\CheckAjax::class)->handle($request, $next);
		}
	}
}

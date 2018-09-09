<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class CheckAjaxUser
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
			return app(\App\Http\Middleware\CheckUser::class)->handle($request, $next, ...$args);
		} catch (AuthenticationException $e) {
			return app(\App\Http\Middleware\CheckAjax::class)->handle($request, $next);
		}
	}
}

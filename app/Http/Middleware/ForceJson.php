<?php

namespace App\Http\Middleware;

use Closure;

class ForceJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	 public function handle(\Illuminate\Http\Request $request, Closure $next)
	 {
		// On force le retour en JSON:
		$request->headers->set('Accept', 'application/json');

		return $next($request);
    }
}

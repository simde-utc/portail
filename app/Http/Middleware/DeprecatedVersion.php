<?php

namespace App\Http\Middleware;

use Closure;

class DeprecatedVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	public function handle(\Illuminate\Http\Request $request, Closure $next, ...$args)
	{
		$response = $next($request);

		// On indique que la version utilisée est encore en bêta
		$response->headers->set(
			config('portail.headers.warn'),
			'La version '.$args[0].' est dépréciée. Préférez utiliser la dernière version stable: '.config('portail.version')
		);

		return $response;
	}
}

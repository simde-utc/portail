<?php

namespace App\Http\Middleware;

use Closure;

class CheckGrantType
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
		$input = $request->all();

        if ($input['grant_type'] === 'client_credentials') {
			$input['scope'] = 'c-get-calendar c-get-assos'; // On override pour l'instant, on gèrera ça plus tard via la bdd

            $request->replace($input);
        }

		return $next($request);
    }
}

<?php
/**
 * Middleware to indicate that it is currently a beta version.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BetaVersion
{
    /**
     * Midify the response.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  $version
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $version)
    {
        $response = $next($request);

        // We indicate that the current version is still in beta.
        $response->headers->set(
	        config('portail.headers.warn'),
	        'La version '.$version.' est en bêta. Préférez utiliser la dernière version stable: '.config('portail.version')
        );

        return $response;
    }
}

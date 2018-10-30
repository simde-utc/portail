<?php
/**
 * Middleware indiquant que nous sommes en version Beta.
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
     * Modifie la réponse.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  $version
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $version)
    {
        $response = $next($request);

        // On indique que la version utilisée est encore en bêta.
        $response->headers->set(
	        config('portail.headers.warn'),
	        'La version '.$version.' est en bêta. Préférez utiliser la dernière version stable: '.config('portail.version')
        );

        return $response;
    }
}

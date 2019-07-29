<?php
/**
 * Middleware to add a header to explain that the current version is deprecated.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DeprecatedVersion
{
    /**
     * Indicate that the version is deprecated in response.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  $version
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $version)
    {
        $response = $next($request);

        // Indicate that the current version is still in beta.
        $response->headers->set(
        config('portail.headers.warn'),
        'La version '.$version.' est dépréciée. Préférez utiliser la dernière version stable: '.config('portail.version')
        );

        return $response;
    }
}

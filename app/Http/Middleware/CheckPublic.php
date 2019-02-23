<?php
/**
 * Middleware vérifiant si la requête est en ajax.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class CheckPublic
{
    /**
     * Vérifie la requête.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Pas de token requis.
        try {
            if ($request->token()) {
                throw new AuthenticationException('Client connecté mais n\'ayant pas les scopes nécessaires');
            }
        } catch (\BadMethodCallException $e) {
            return $next($request);
        }
    }
}

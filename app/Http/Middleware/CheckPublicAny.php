<?php
/**
 * Middleware vérifiant si la requête vient d'un client oauth ou est en ajax.
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

class CheckPublicAny
{
    /**
     * Vérifie si c'est un client oauth ou si la requête est en ajax.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        try {
            return app(\App\Http\Middleware\CheckAny::class)->handle($request, $next, ...$args);
        } catch (AuthenticationException $e) {
            return app(\App\Http\Middleware\CheckPublic::class)->handle($request, $next);
        }
    }
}

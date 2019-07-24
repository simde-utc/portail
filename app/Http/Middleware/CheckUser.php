<?php
/**
 * Middleware vérifiant si la requête vient d'un client oauth user.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Illuminate\Http\Request;

class CheckUser
{
    /**
     * Vérifie si c'est un client oauth user.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        if (count($args) > 1) {
            if (empty($args[1])) {
                throw new AuthenticationException('Route uniquement utilisable par un client OAuth2');
            }

            if ($args[0] == 0) {
                $scopes = [];

                foreach (explode('|', $args[1]) as $scope) {
                    array_push($scopes, \Scopes::getMatchingScopes(explode('|', $scope), 'user'));
                }
            } else {
                $scopes = [\Scopes::getMatchingScopes(explode('|', $args[1]), 'user')];
            }

            $checkScopes = function ($request) use ($next, $scopes) {
                if (!$request->user() || !$request->user()->token()) {
                    throw new AuthenticationException;
                }

                if (!$request->user()->token()->transient()) {
                    $tokenScopes = $request->user()->token()->scopes;

                    // On vérifie pour chaque ensemble de scopes.
                    foreach ($scopes as $scopeList) {
                        // Qu'on en possède au moins un parmi la liste.
                        if (empty(array_intersect($tokenScopes, $scopeList))) {
                            throw new MissingScopeException($scopes);
                        }
                    }
                }

                return $next($request);
            };
        } else {
            $checkScopes = function ($request) use ($next) {
                if (!$request->user() || !$request->user()->token()) {
                    throw new AuthenticationException;
                }

                return $next($request);
            };
        }

        // Useless to reconnect the user if it is already connected.
        if (!$request->user()) {
            return app(Authenticate::class)->handle($request, $checkScopes, 'api');
        }

        return $checkScopes($request);
    }
}

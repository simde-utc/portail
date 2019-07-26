<?php
/**
 * Middleware to check if the request comes from a client OAuth client.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;
use Illuminate\Http\Request;

class CheckClient
{
    /**
     * Check if it's a client OAuth client.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        // We check that the token isxn't linked to a user.
        $bearerToken = $request->bearerToken();
        $tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
        $token = Token::find($tokenId);

        if ($token === null || $token->user_id !== null) {
            throw new AuthenticationException;
        }

        if (count($args) > 0) {
            if (empty($args[1])) {
                throw new AuthenticationException('Route uniquement utilisable par un client connecté via un utilisateur');
            }

            if ($args[0] == 0) {
                 $scopes = [];

                foreach (explode('|', $args[1]) as $scope) {
                    array_push($scopes, \Scopes::getMatchingScopes(explode('|', $scope), 'user'));
                }
            } else {
                $scopes = [\Scopes::getMatchingScopes(explode('|', $args[1]), 'client')];
            }

            return app(CheckClientCredentials::class)->handle($request, function ($request) use ($next, $scopes, $token) {
                if ($token->transient()) {
                    throw new AuthenticationException;
                }

                $tokenScopes = $token->scopes;

                // We check for each scope.
                foreach ($scopes as $scopeList) {
                    // That one in the list is owned.
                    if (empty(array_intersect($tokenScopes, $scopeList))) {
                        throw new MissingScopeException($scopes);
                    }
                }

                return $next($request);
            });
        } else {
            return app(CheckClientCredentials::class)->handle($request, $next);
        }
    }
}

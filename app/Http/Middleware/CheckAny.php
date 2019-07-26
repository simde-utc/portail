<?php
/**
 * Middleware to check if the request comes from an OAuth client.
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
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Http\Request;

class CheckAny
{
    /**
     * Check if it's an OAuth client.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        // We check that the token isxn't linked to a user.
        if ($request->bearerToken() !== null && !$request->isAFakedUserRequest) {
            $bearerToken = $request->bearerToken();
            $tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
            $token = Token::find($tokenId);

            if ($token !== null && $token->user_id === null) {
                unset($args[1]);
                return app(\App\Http\Middleware\CheckClient::class)->handle($request, $next, ...$args);
            }
        }

        unset($args[2]);
        return app(\App\Http\Middleware\CheckUser::class)->handle($request, $next, ...$args);
    }
}

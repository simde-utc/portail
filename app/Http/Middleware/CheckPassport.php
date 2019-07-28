<?php
/**
 * Middleware to handle scopes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\{
    Client, User
};
use Illuminate\Http\Request;

class CheckPassport
{
    /**
     * Check or handle scopes depending on the OAuth request type.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        $clientId = ($input['client_id'] ?? $request->header('PHP_AUTH_USER', null));
        $client = Client::find($clientId);

        // If we cannot retrieve client_id, access is refused.
        if ($clientId === null || $client === null) {
            throw OAuthServerException::accessDenied();
        }

        // We check the request only if it's a client authentication.
        if ($request->input('grant_type') === 'client_credentials') {
            $this->checkClientCredentials($request, $client, $input);
        }

        // Magical method to simplify the development.
        if (isset($input['scope']) && $input['scope'] === '*' && config('app.debug')) {
            $input['scope'] = implode(' ', \Scopes::getDevScopes());

            $request->replace($input);
        }

        // We check that the scopes are defined and for the right authetication type.
        if (isset($input['scope']) && ($input['scope'] !== '')) {
            \Scopes::checkScopesForGrantType(explode(' ', $input['scope']), ($input['grant_type'] ?? null));
        } else if (isset($input['scopes']) && ($input['scopes'] !== '')) {
            \Scopes::checkScopesForGrantType($input['scopes'], ($input['grant_type'] ?? null));
        }

        if (\Auth::id()) {
            // We check if the application is not only for the developement and to the association itself.
            if ($client->restricted) {
                if ($client->asso) {
                    if (!$client->asso->hasOneMember(\Auth::id())) {
                        return response(view('auth.passport.denied', [
                            'types' => ['Il est nécessaire d\'être de l\'association pour accéder à cette application'],
                            'client' => $client,
                            'request' => $request,
                        ]), 403);
                    }
                }
            }

            // We check if the application is not only for a public.
            if ($client->targeted_types) {
                $targets = json_decode($client->targeted_types, true);

                try {
                    return (new UserIs)->handle($request, $next, ...$targets);
                } catch (AuthorizationException $e) {
                    $types = array_intersect_key((new User)->getTypeDescriptions(), array_flip($targets));

                    return response(view('auth.passport.denied', [
                        'types' => array_values($types),
                        'client' => $client,
                        'request' => $request,
                    ]), 403);
                }
            }
        }

        return $next($request);
    }

    /**
     * Check in the client request type that the scopes are not defined.
     * @param  Request $request
     * @param  Client  $client
     * @param  array   $input
     * @return void
     */
    protected function checkClientCredentials(Request $request, Client $client, array $input)
    {
        if ($request->filled('scope')) {
            throw new \Exception('Les scopes sont définis à l\'avance pour chaque clé.
                Il ne faut pas les définir dans la requête');
        }

        // We retrieve the client defined scopes list.
        $scopes = json_decode($client->scopes, true);

        if ($scopes === null) {
            $input['scope'] = '';
        } else {
            // We override for now, it will be handle later through the DB.
            $input['scope'] = implode(' ', $scopes);
        }

        $request->replace($input);
    }
}

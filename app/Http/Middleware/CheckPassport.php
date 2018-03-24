<?php

namespace App\Http\Middleware;

use Closure;
use League\OAuth2\Server\Exception\OAuthServerException;
use Laravel\Passport\Client;

class CheckPassport
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

        if (isset($input['grant_type']) && $input['grant_type'] === 'client_credentials') {
			if (isset($input['scope']))
				throw new \Exception('Les scopes sont définis à l\'avance pour chaque clé, il ne faut pas les définir dans la requête');

			$clientId = $_SERVER['PHP_AUTH_USER'] ?? $_REQUEST['client_id'] ?? null;
			$client = Client::find($clientId);

			if ($clientId === null || $client === null)
				throw OAuthServerException::accessDenied();

			$input['scope'] = implode(' ', json_decode($client->first()->scopes, true)); // On override pour l'instant, on gèrera ça plus tard via la bdd

            $request->replace($input);
        }

		if (isset($input['scope']))
			\Scopes::checkScopesForGrantType(explode(' ', $input['scope']), $input['grant_type'] ?? null); // On vérifie que les scopes sont bien définis

		return $next($request);
    }
}

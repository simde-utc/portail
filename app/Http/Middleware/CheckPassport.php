<?php
/**
 * Middleware gérant les scopes.
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
     * Vérifie ou manipule les scopes en fonction du type de demande oauth.
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

        // Si on n'arrive pas à récupérer le client_id, on refuse l'accès.
        if ($clientId === null || $client === null) {
            throw OAuthServerException::accessDenied();
        }

        // On vérifie la requête uniquement s'il s'agit d'une authentification par client uniquement.
        if ($request->input('grant_type') === 'client_credentials') {
            $this->checkClientCredentials($request, $client, $input);
        }

        // Méthode magique pour simplifier le dev.
        if (isset($input['scope']) && $input['scope'] === '*' && config('app.debug')) {
            $input['scope'] = implode(' ', \Scopes::getDevScopes());

            $request->replace($input);
        }

        // On vérifie que les scopes sont bien définis et pour le bon type d'authentification.
        if (isset($input['scope']) && ($input['scope'] !== '')) {
            \Scopes::checkScopesForGrantType(explode(' ', $input['scope']), ($input['grant_type'] ?? null));
        } else if (isset($input['scopes']) && ($input['scopes'] !== '')) {
            \Scopes::checkScopesForGrantType($input['scopes'], ($input['grant_type'] ?? null));
        }

        if (\Auth::id()) {
            // On vérifie si l'application n'est pas restreinte à du développement et à l'association en elle-même.
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

            // On vérifie si l'application n'est pas réduite à un certain public.
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
     * Vérifie le type de demande client que les scopes ne sont pas définis.
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

        // On récupère la liste des scopes définis pour le client.
        $scopes = json_decode($client->scopes, true);

        if ($scopes === null) {
            $input['scope'] = '';
        } else {
            // On override pour l'instant, on gèrera ça plus tard via la bdd.
            $input['scope'] = implode(' ', $scopes);
        }

        $request->replace($input);
    }
}

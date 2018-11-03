<?php
/**
 * Middleware vérifiant si l'utilisateur est connecté.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use App\Models\Session;
use App\Models\Passport\TokenSession;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;

class CheckAuth
{
    /**
     * Vérifie si l'utilisateur est bien connecté
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // On vérifie que l'utilisateur lié au token est toujours connecté.
        if ($request->user() !== null) {
            $token = $request->user()->token();

            if (!$token->transient()) {
                $client = Client::find($token->client_id);

                // On vérifie uniquement pour les tokens qui ne sont pas un token personnel ou lié à une application sans session.
                if ($client !== null && !$client->personal_access_client && !$client->password_client) {
                    if (!$request->user()->sessions()->exists()) {
                        abort(410, 'L\'utilisateur n\'est plus connecté');
                    }
                }
            }
        }

        return $next($request);
    }
}

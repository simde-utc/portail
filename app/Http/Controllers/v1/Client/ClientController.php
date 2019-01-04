<?php
/**
 * Gère les clients de l'utilisateur
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Client;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

class ClientController extends Controller
{
    /**
     * Liste les informations (scopes) sur le client en cours.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(\Scopes::getClient($request));
    }

    /**
     * Retourne les users qui ont authorisé les actions du client en cours.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function getUsersClient(Request $request): JsonResponse
    {
        $client = \Scopes::getClient($request);
        $tokens = Token::where('client_id', $client->id);

        if ($request->input('revoked')) {
            $tokens->where('revoked', $request->input('revoked') == 1 ? 1 : 0);
        }

        $result = [];

        foreach ($tokens->get()->makeHidden('id')->makeHidden('session_id')->groupBy('user_id') as $user_id => $tokenList) {
            $scopes = [];

            foreach ($tokenList as $token) {
                foreach ($token->scopes as $scope) {
                    $scopes[$scope] = '';
                }
            }

            $result[] = [
                'user_id' => $user_id === '' ? null : $user_id,
                'scopes'  => array_keys($scopes),
            ];
        }

        return response()->json($result);
    }

    /**
     * Retourne les users qui ont authorisé les actions du client en cours.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function getUserClient(Request $request, string $user_id): JsonResponse
    {
        $tokens = Token::where('client_id', \Scopes::getClient($request)->id)->where('user_id', $user_id);

        if ($request->input('revoked')) {
            $tokens->where('revoked', $request->input('revoked') === '1' ? 1 : 0);
        }

        $tokens = $tokens->get()->makeHidden('id')->makeHidden('session_id');

        return response()->json($tokens);
    }

    /**
     * Supprime les autorisations d'un utilisateur pour le client.
     *
     * @param Request $request
     * @param string  $user_id
     * @return void
     */
    public function destroy(Request $request, string $user_id): void
    {
        if (Token::where('client_id', \Scopes::getClient($request)->id)
            ->where('user_id', $user_id)->where('revoked', false)->delete()) {
            abort(202, 'Tokens associés à l\'utilisateur supprimés avec succès');
        }

        abort(404, 'Aucun token supprimé');
    }

    /**
     * Supprime les autorisations de tous les utilisateurs pour le client.
     *
     * @param Request $request
     * @return void
     */
    public function destroyAll(Request $request): void
    {
        if (Token::where('client_id', \Scopes::getClient($request)->id)->where('revoked', false)->delete()) {
            abort(202, 'Tokens associés à l\'utilisateur supprimés avec succès');
        }

        abort(404, 'Aucun token supprimé');
    }

    /**
     * Supprime les autorisations pour l'utilisateur courant.
     *
     * @param Request $request
     * @return void
     */
    public function destroyCurrent(Request $request): void
    {
        Token::where('client_id', \Scopes::getClient($request)->id)->where('user_id', $request->user()->id)
            ->where('revoked', false)->delete();

        abort(202, 'Tokens associés à l\'utilisateur supprimés avec succès');
    }
}

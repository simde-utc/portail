<?php
/**
 * Gère les clients de l'utilisateur
 *
 * TODO: En abort
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
        $bearerToken = $request->bearerToken();
        $tokenId = (new Parser)->parse($bearerToken)->getHeader('jti');
        $client = Token::find($tokenId)->client->toArray();

        $client['scopes'] = json_decode($client['scopes'], true);

        return response()->json($client);
    }

    /**
     * Retourne les users qui ont authorisé les actions du client en cours.
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function getUsersClient(Request $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();
        $tokenId = (new Parser)->parse($bearerToken)->getHeader('jti');
        $clientId = Token::find($tokenId)->client_id;
        $tokens = Token::where('client_id', $clientId);

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

            array_push($result, [
                'user_id' => $user_id === '' ? null : $user_id,
                'scopes'  => array_keys($scopes),
            ]);
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
        $bearerToken = $request->bearerToken();
        $token_id = (new Parser)->parse($bearerToken)->getHeader('jti');
        $client_id = Token::find($tokenId)->client_id;
        $tokens = Token::where('client_id', $client_id)->where('user_id', $user_id);

        if ($request->input('revoked')) {
            $tokens->where('revoked', $request->input('revoked') == 1 ? 1 : 0);
        }

        return response()->json($tokens->get()->makeHidden('id')->makeHidden('session_id'));
    }

    /**
     * Supprime les autorisations d'un utilisateur pour le client.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $user_id): JsonResponse
    {
        $bearerToken = $request->bearerToken();
        $tokenId = (new Parser)->parse($bearerToken)->getHeader('jti');
        $clientId = Token::find($tokenId)->client_id;

        if (Token::where('client_id', $clientId)->where('user_id', $user_id)->where('revoked', false)->delete() === 0) {
            return response()->json(['message' => 'Aucun token supprimé'], 404);
        } else {
            return response()->json(['message' => 'Tokens associés à l\'utilisateur supprimés avec succès'], 202);
        }
    }

    /**
     * Supprime les autorisations de tous les utilisateurs pour le client.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $bearerToken = $request->bearerToken();
        $tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
        $clientId = Token::find($tokenId)->client_id;

        if (Token::where('client_id', $clientId)->where('revoked', false)->delete() === 0) {
            return response()->json(['message' => 'Aucun token supprimé'], 404);
        } else {
            return response()->json(['message' => 'Tous vos tokens ont été supprimés avec succès'], 202);
        }
    }

    /**
     * Supprime les autorisations pour l'utilisateur courant.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyCurrent(Request $request): JsonResponse
    {
        Token::where('client_id', $request->user()->token()->client_id)->where('user_id', $request->user()->id)
			->where('revoked', false)->delete();

        return response()->json(['message' => 'Tokens associés à l\'utilisateur supprimés avec succès'], 202);
    }
}

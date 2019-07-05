<?php
/**
 * Ajoute au controlleur une gestion des bulks avec des utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\User;

trait HasUserBulkMethods
{
    protected $allowedUsers = [];

    /**
     * Gère les différents appels pour chaque élément bulk. Restreint par rapport aux utilisateurs.
     *
     * @param  Request $request
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    protected function callForBulk(Request $request, string $method, array $args)
    {
        $tokens = \Scopes::getClient($request)->tokens()
            ->groupBy('user_id')->orderBy('expires_at', 'DESC')
            ->where('revoked', false)->get();

        User::whereIn('id', $tokens->pluck('user_id'))->get()->map(function ($user) use ($tokens) {
            $user->withAccessToken($tokens->first(function ($token) use ($user) {
                return $token->user_id === $user->id;
            }));

            $this->allowedUsers[$user->id] = $user;
        });

        return parent::callForBulk($request, $method, $args);
    }

    /**
     * Retourne la réponse pour un élément du bulk avec les informations d'un utilisateur précis.
     *
     * @param  string  $method
     * @param  Request $request
     * @param  array   $args
     * @return mixed
     */
    protected function getResponseForBulk(string $method, Request $request, array $args)
    {
        $user = ($this->allowedUsers[$args[0]] ?? null);

        if (\is_null($user)) {
            abort(403, "L'utilisateur n'existe pas ou n'est pas accessible par ce client");
        }

        // Here, we fake a user request.
        $lastUserResolver = $request->getUserResolver();
        $request->setUserResolver(function ($guard) use ($user, $lastUserResolver) {
            if ($guard) {
                return $lastUserResolver($guard);
            }

            return $user;
        });

        return parent::getResponseForBulk($method, $request, $args);
    }
}

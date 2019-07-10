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
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\{
    MiddlewareNameResolver, SortedMiddleware
};
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
        $tokens = \Scopes::getClient($request)->tokens()->whereNotNull('user_id')
            ->orderBy('updated_at', 'DESC')->where('revoked', false)->get();

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
     * @param  Request $request
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    protected function getResponseForBulk(Request $request, string $method, array $args)
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

        return parent::getResponseForBulk($request, $method, $args);
    }

    /**
     * Execute la méthode pour un élément de bulk en passant les bons middlewares avant.
     *
     * @param  Request $request
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    protected function executeMethodForBulk(Request $request, string $method, array $args)
    {
        $router = app('router');
        $middlewares = $router->getMiddleware();
        $middlewareGroups = $router->getMiddlewareGroups();
        $middlewaresPriority = $router->middlewarePriority;

        $resolveMiddleware = function ($name) use ($middlewares, $middlewareGroups) {
            return (array) MiddlewareNameResolver::resolve($name, $middlewares, $middlewareGroups);
        };
        $middlewaresToPass = collect($this->resolveMiddlewares($method))->map($resolveMiddleware)->flatten();

        $request->isAFakedUserRequest = true;

        return app(Pipeline::class)
            ->send($request)
            ->through((new SortedMiddleware($middlewaresPriority, $middlewaresToPass))->all())
            ->then(function ($request) use ($method, $args) {
                return parent::executeMethodForBulk($request, $method, $args);
            });
    }

    /**
     * Résolution des middlewares pour une méthode précise.
     *
     * @param  string $method
     * @return array
     */
    protected function resolveMiddlewares(string $method): array
    {
        $middlewares = [];

        foreach ($this->middleware as $element) {
            $options = $element['options'];
            $addMiddlewares = (isset($options['except']) && !in_array($method, $options['except'])) ||
                isset($options['only']) && in_array($method, $options['only']);

            if ($addMiddlewares) {
                $middlewares = array_merge($middlewares, (array) $element['middleware']);
            }
        }

        return $middlewares;
    }
}

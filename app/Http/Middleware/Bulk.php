<?php
/**
 * Middlewar to redirect to the right action by executing the right middlewares.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\{
    Route, MiddlewareNameResolver, SortedMiddleware
};

class Bulk
{
    protected $oneActions = [
        'all' => 'index',
        'create' => 'store',
        'get' => 'show',
        'edit' => 'update',
        'remove' => 'destroy',
    ];

    protected $bulkActions = [
        'all' => 'bulkIndex',
        'create' => 'bulkStore',
        'get' => 'bulkShow',
        'edit' => 'bulkUpdate',
        'remove' => 'bulkDestroy',
    ];

    /**
     * Redirect if the request is a bulk one.
     *
     * @param  Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        foreach ($route->parameters() as $paramValue) {
            if (\substr($paramValue, 0, 1) === '[' && \substr($paramValue, -1) === ']') {
                return $this->callRightAction($route, $next, $request, $this->bulkActions);
            }
        }

        return $this->callRightAction($route, $next, $request, $this->oneActions);
    }

    /**
     * Call the right action depending on a given mapping array.
     *
     * @param  Route   $route
     * @param  Closure $next
     * @param  Request $request
     * @param  array   $actions
     * @return mixed
     */
    protected function callRightAction(Route $route, Closure $next, Request $request, array $actions)
    {
        [$controller, $action] = explode('@', $route->getActionName());
        $route->uses($controller.'@'.$actions[$action]);

        return $this->executeControllerMiddlewares($route, $next, $request);
    }

    /**
     * Execute the right controller after having passed all middlewares.
     *
     * @param  Route   $route
     * @param  Closure $next
     * @param  Request $request
     * @return mixed
     */
    protected function executeControllerMiddlewares(Route $route, Closure $next, Request $request)
    {
        $router = app('router');
        $middlewares = $router->getMiddleware();
        $middlewareGroups = $router->getMiddlewareGroups();
        $middlewaresPriority = $router->middlewarePriority;

        $resolveMiddleware = function ($name) use ($middlewares, $middlewareGroups) {
            return (array) MiddlewareNameResolver::resolve($name, $middlewares, $middlewareGroups);
        };
        $middlewaresToPass = collect($route->controllerMiddleware())->map($resolveMiddleware)->flatten();

        return app(Pipeline::class)
            ->send($request)
            ->through((new SortedMiddleware($middlewaresPriority, $middlewaresToPass))->all())
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }
}

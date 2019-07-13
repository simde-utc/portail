<?php
/**
 * Ajoute au controlleur une gestion des bulks.
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

trait HasBulkMethods
{
    /**
     * Retourne tous les éléménts d'une ou plusieurs ressources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        return $this->callOneOrBulk($request, 'index');
    }

    /**
     * Crée un ou plusieurs élément d'une ressource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        return $this->callOneOrBulk($request, 'store');
    }

    /**
     * Récupère un ou plusieurs élément d'une ressource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        return $this->callOneOrBulk($request, 'show');
    }

    /**
     * Récupère un ou plusieurs élément d'une ressource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function edit(Request $request)
    {
        return $this->callOneOrBulk($request, 'update');
    }

    /**
     * Supprime un ou plusieurs élément d'une ressource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function remove(Request $request)
    {
        return $this->callOneOrBulk($request, 'destroy');
    }

    /**
     * Retourne la réponse pour un ou plusieurs éléments.
     *
     * @param  Request $request
     * @param  string  $method
     * @return mixed
     */
    public function callOneOrBulk(Request $request, string $method)
    {
        $route = $request->route();

        foreach ($route->parameters() as $paramValue) {
            if (\substr($paramValue, 0, 1) === '[' && \substr($paramValue, -1) === ']') {
                return $this->{'bulk'.ucfirst($method)}($request);
            }
        }

        $route->uses(static::class.'@'.$method);

        return Route::dispatch($request);
    }

    /**
     * Affiche plusieurs ressources en même temps.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkIndex(Request $request)
    {
        return $this->callBulk($request, 'index');
    }

    /**
     * Affiche plusieurs ressources en même temps.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkShow(Request $request)
    {
        return $this->callBulk($request, 'show');
    }

    /**
     * Crée plusieurs ressources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkStore(Request $request)
    {
        return $this->callBulk($request, 'store');
    }

    /**
     * Met à jour plusieurs ressources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkUpdate(Request $request)
    {
        return $this->callBulk($request, 'update');
    }

    /**
     * Supprime plusieurs ressources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkDestroy(Request $request)
    {
        return $this->callBulk($request, 'destroy');
    }

    /**
     * Gestion d'une requête bulk pour une méthode spécifique.
     *
     * @param  Request $request
     * @param  string  $method
     * @return mixed
     */
    protected function callBulk(Request $request, string $method)
    {
        $params = $this->cleanRouteParams($request->route()->parameters());
        $bulkArgs = [];

        foreach ($params as $param) {
            if ($param[0] === '[' && $param[(strlen($param) - 1)] === ']') {
                $param = substr($param, 1, -1);
                $parts = \explode(',', $param);

                if (count($parts) === 1) {
                    $bulkArgs[] = $request->input($param, $parts);
                } else {
                    $bulkArgs[] = $parts;
                }
            } else {
                $bulkArgs[] = [$param];
            }
        }

        return $this->callForBulk($request, $method, $bulkArgs);
    }

    /**
     * Gère les différents appels pour chaque élément bulk.
     *
     * @param  Request $request
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    protected function callForBulk(Request $request, string $method, array $args)
    {
        $majorStatus = null;
        $responses = $this->callForEachBulk($args, function ($stack) use ($request, $method, &$majorStatus) {
            try {
                $response = $this->getResponseForBulk($request, $method, $stack);
            } catch (\Exception $e) {
                app(ExceptionHandler::class)->report($e);

                $response = app(ExceptionHandler::class)->render($request, $e);
            }

            $status = $response->status();

            // Indicate if we have different status.
            if ($majorStatus !== $status) {
                $majorStatus = is_null($majorStatus) ? $status : 207;
            }

            return [
                'data' => $response->getOriginalContent(),
                'status' => $response->status(),
            ];
        });

        return response($responses, ($majorStatus ?? 200));
    }

    /**
     * Exécute la requête pour chaque bulk argument.
     *
     * @param  array    $args
     * @param  \Closure $callback
     * @param  array    $stack
     * @return array
     */
    protected function callForEachBulk(array $args, \Closure $callback, array $stack=[]): array
    {
        if (count($args)) {
            $result = [];

            foreach (array_shift($args) as $arg) {
                $result[$arg] = $this->callForEachBulk($args, $callback, array_merge($stack, [$arg]));
            }

            return $result;
        }

        return $callback($stack);
    }

    /**
     * Retourne la réponse pour un élément du bulk.
     *
     * @param  Request $request
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    protected function getResponseForBulk(Request $request, string $method, array $args)
    {
        $reflection = new \ReflectionMethod(static::class, $method);
        $params = [];
        $hasRequest = false;

        foreach ($reflection->getParameters() as $key => $param) {
            if (($type = $param->getType())->isBuiltin()) {
                $params[] = $args[($key - $hasRequest)];
            } else {
                $params[] = $this->getRequestForBulk($type->getName(), $request, $args);
                $hasRequest = true;
            }
        }

        return $this->executeMethodForBulk($request, $method, $params);
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
        return $this->$method(...$args);
    }

    /**
     * Retourne la requête qui doit être exécuté pour élément du bulk.
     *
     * @param  string  $requestClass
     * @param  Request $baseRequest
     * @param  array   $args
     * @return Request
     */
    protected function getRequestForBulk(string $requestClass, Request $baseRequest, array $args): Request
    {
        $request = $requestClass::createFromBase($baseRequest);

        $request->setUserResolver($baseRequest->getUserResolver());
        $request->setRouteResolver($baseRequest->getRouteResolver());

        // Define the inputs for a specific request.
        if ($inputs = $request->input(\implode('.', $args))) {
            $request->merge($inputs);
        }

        $params = $this->cleanRouteParams($request->route()->parameters());

        // We need to define each parameters for this bulk.
        foreach (array_keys($params) as $key => $paramName) {
            $request->$paramName = $args[$key];
        }

        // Pass validations.
        $this->validateRequestForBulk($request);

        return $request;
    }

    /**
     * Retourne les paramètres nettoyés de la route.
     *
     * @param  array $params
     * @return array
     */
    protected function cleanRouteParams(array $params): array
    {
        // We need to avoid bulk queries on resource type, easy conflicts...
        if (isset($params['resource_type'])) {
            unset($params['resource_type']);
        }

        return $params;
    }

    /**
     * Valide ou non les requêtes pour chaque élement du bulk.
     *
     * @param  Request $request
     * @return void
     */
    protected function validateRequestForBulk(Request $request)
    {
        if (\method_exists($request, 'authorize') && !$request->authorize()) {
            throw new AuthorizationException('This action is unauthorized.');
        }

        if (\method_exists($request, 'rules')) {
            $validator = \Validator::make($request->all(), $request->rules());
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }
}

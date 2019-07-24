<?php
/**
 * Add the controller the bulks management.
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
     * Return all elements of one or several resources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        return $this->callOneOrBulk($request, 'index');
    }

    /**
     * Create one or several elements of a resource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        return $this->callOneOrBulk($request, 'store');
    }

    /**
     * Retrieve one or several elements of a resource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function get(Request $request)
    {
        return $this->callOneOrBulk($request, 'show');
    }

    /**
     * Edit one or several elements of a resource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function edit(Request $request)
    {
        return $this->callOneOrBulk($request, 'update');
    }

    /**
     * Remove one or several elements of a resource.
     *
     * @param  Request $request
     * @return mixed
     */
    public function remove(Request $request)
    {
        return $this->callOneOrBulk($request, 'destroy');
    }

    /**
     * Return the response for one or several elements.
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
     * Display several resources at the same time.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkIndex(Request $request)
    {
        return $this->callBulk($request, 'index');
    }

    /**
     * Display several resources at the same time.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkShow(Request $request)
    {
        return $this->callBulk($request, 'show');
    }

    /**
     * Create several resources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkStore(Request $request)
    {
        return $this->callBulk($request, 'store');
    }

    /**
     * Update several resources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkUpdate(Request $request)
    {
        return $this->callBulk($request, 'update');
    }

    /**
     * Destroy several resources.
     *
     * @param  Request $request
     * @return mixed
     */
    public function bulkDestroy(Request $request)
    {
        return $this->callBulk($request, 'destroy');
    }

    /**
     * Bulk request for a given method management.
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
     * Handle different calls for each bulk element.
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
     * Execute the request for each bulk argument.
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
     * Return the response for a bulk element.
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
     * Execute the method for a bulk element by passing the right middlewares before.
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
     * Return the request wich must be executed for a element of the bulk.
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
     * Return the parameters cleaned from the route.
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
     * Validate or not requests for each element of the bulk. 
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

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

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;

trait HasBulkMethods
{
    /**
     * Affiche plusieurs ressources en même temps.
     *
     * @param  Request $request
     * @param  mixed   ...$args
     * @return mixed
     */
    public function bulkShow(Request $request, ...$args)
    {
        return $this->callBulk($request, $args, 'show');
    }

    /**
     * Crée plusieurs ressources.
     *
     * @param  Request $request
     * @param  mixed   ...$args
     * @return mixed
     */
    public function bulkStore(Request $request, ...$args)
    {
        return abort(405, 'Les créations ne sont pas encore gérées en bulk');
    }

    /**
     * Met à jour plusieurs ressources.
     *
     * @param  Request $request
     * @param  mixed   ...$args
     * @return mixed
     */
    public function bulkUpdate(Request $request, ...$args)
    {
        return abort(405, 'Les mises à jour ne sont pas encore gérées en bulk');
    }

    /**
     * Supprime plusieurs ressources.
     *
     * @param  Request $request
     * @param  mixed   ...$args
     * @return mixed
     */
    public function bulkDestroy(Request $request, ...$args)
    {
        return abort(405, 'Les suppressions ne sont pas encore gérées en bulk');
    }

    /**
     * Gestion d'une requête bulk pour une méthode spécifique.
     *
     * @param  Request $request
     * @param  array   $args
     * @param  string  $method
     * @return mixed
     */
    protected function callBulk(Request $request, array $args, string $method)
    {
        $bulkArgs = [];
        $majorStatus = null;

        foreach ($args as $arg) {
            $bulkArgs[] = \explode(',', $arg);
        }

        $responses = $this->callForEachBulkArg($bulkArgs, function ($stack) use ($request, $method, &$majorStatus) {
            try {
                $response = $this->$method($request, ...$stack);
            } catch (\Exception $e) {
                app(ExceptionHandler::class)->report($e);

                $response = app(ExceptionHandler::class)->render($request, $e);
            } catch (\Error $e) {
                app(ExceptionHandler::class)->report($e);

                $response = app(ExceptionHandler::class)->render($request, $e);
            }

            $status = $response->status();

            // Si on a différents status, on l'indique.
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
    protected function callForEachBulkArg(array $args, \Closure $callback, array $stack=[])
    {
        if (count($args)) {
            $result = [];

            foreach (array_shift($args) as $arg) {
                $result[$arg] = $this->callForEachBulkArg($args, $callback, array_merge($stack, [$arg]));
            }

            return $result;
        }

        return $callback($stack);
    }
}

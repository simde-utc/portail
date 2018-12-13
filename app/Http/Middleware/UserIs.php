<?php
/**
 * Middleware permettant de détecter si l'utilsateur connecté est d'un certain type.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class UserIs
{
    /**
     * Si l'utilisateur connecté n'est pas d'un des types requis, il est interdit d'accès.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        if (count($args) === 0) {
            throw new \Exception('Il est nécessaire de spécifier au moins un type');
        }

        if (\Auth::id()) {
            foreach ($args as $type) {
                if ($wrongIfEqual = ($type[0] === '!')) {
                    $type = substr($type, 1);
                }

                $method = 'is'.ucfirst($type);

                if (!method_exists(\Auth::user(), $method)) {
                    throw new \Exception('Le type '.$type.' n\'existe pas !');
                }

                if (\Auth::user()->$method() !== $wrongIfEqual) {
                    return $next($request);
                }
            }

            $descriptions = array_map(function ($type) {
                return \Auth::user()->getTypeDescriptions()[$type];
            }, $args);

            throw new AuthorizationException('L\'utilisateur n\'est d\'aucun type: '.implode(', ', $descriptions));
        }

        return $next($request);
    }
}

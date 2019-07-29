<?php
/**
 * Middleware to detect if the connected user has a permission.
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

class UserHasPermission
{
    /**
     * If the connected user does not have any of the required permissions, access is refused.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        if (count($args) === 0) {
            throw new \Exception('Il est nécessaire de spécifier au moins un permission');
        }

        if (\Auth::id()) {
            foreach ($args as $permission) {
                if ($wrongIfEqual = ($permission[0] === '!')) {
                    $permission = substr($permission, 1);
                }

                if (\Auth::user()->hasOnePermission($permission) !== $wrongIfEqual) {
                    return $next($request);
                }
            }

            throw new AuthorizationException('L\'utilisateur ne possède aucune permission: '.implode(', ', $args));
        }

        return $next($request);
    }
}

<?php
/**
 * Middleware to detect if the connected user has a role.
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

class UserHasRole
{
    /**
     * If the connected user does not have any of the required, access is refused.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  string  ...$args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$args)
    {
        if (count($args) === 0) {
            throw new \Exception('Il est nécessaire de spécifier au moins un rôle');
        }

        if (\Auth::id()) {
            foreach ($args as $role) {
                if ($wrongIfEqual = ($role[0] === '!')) {
                    $role = substr($role, 1);
                }

                if (\Auth::user()->hasOneRole($role) !== $wrongIfEqual) {
                    return $next($request);
                }
            }

            throw new AuthorizationException('L\'utilisateur ne possède aucun rôle: '.implode(', ', $args));
        }

        return $next($request);
    }
}

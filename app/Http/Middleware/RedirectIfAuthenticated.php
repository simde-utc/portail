<?php
/**
 * Middleware to check if a user is connected.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Redidirect the user if he's connected.
     *
     * @param  Request     $request
     * @param  Closure     $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $guard=null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }

        return $next($request);
    }
}

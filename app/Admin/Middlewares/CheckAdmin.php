<?php
/**
 * Vérifie les droits d'accéder à la page admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Middlewares;

use Encore\Admin\Auth\Permission as Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     * @param mixed    ...$args
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args)
    {
        if (!\Admin::user() || !empty($args)) {
            return $next($request);
        }

        $path = Str::after($request->route()->uri, config('admin.route.prefix'));
        $pathList = explode('/', $path);

        $extension = ($pathList[0] ?: ($pathList[1] ?? null));

        if ($extension) {
            $menu = config('admin.database.menu_model')::where('uri', $extension)->first();

            if ($menu) {
                foreach (stringToArray($menu->permission) as $permission) {
                    if (\Admin::user()->can($permission)) {
                        return $next($request);
                    }
                }

                Checker::error();
            }
        }

        return $next($request);
    }
}

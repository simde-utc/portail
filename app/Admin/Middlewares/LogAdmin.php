<?php
/**
 * Log les accés à la page admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Middlewares;

use Illuminate\Http\Request;

class LogAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $user = (\Auth::guard('admin')->user() ?? \Auth::guard('web')->user());

        $data = ['exception' => [
            'method' => $request->method(),
            'route' => $request->path(),
            'query' => $request->query(),
            'input' => $request->input(),
            'cookie' => $request->cookie(),
        ]
        ];

        \Log::channel('admin')->info($user->name.' (id: '.$user->id.') '.json_encode($data));

        return $next($request);
    }
}

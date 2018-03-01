<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Support\Facades\Auth;

class Cas
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user === null)
            return redirect('login', ['provider' => 'cas', 'redirection' => url()->current()]);
        else if ($user->utc())
            return $next($request);
       	else
            return redirect('login', ['provider'], 'cas');
    }
}

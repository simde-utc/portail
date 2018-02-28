<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\Cas;

class CheckCas
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
        if (Auth::check())
            return $next($request);
       	else            // If no user data found in Session, login and redirect to
            return Cas::login(route('login.cas'));
    }
}

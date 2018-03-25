<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Passport\AuthCode;
use Laravel\Passport\Token;

class LinkTokenToSession
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
		$response = $next($request);

		if ($response->status() != 200) {
			$code = AuthCode::where('user_id', $request->user()->id)->where('client_id', $request->input('client_id'))->orderBy('expires_at', 'DESC')->first();
			$code->session_id = \Session::getId();
			$code->timestamps = false;
			$code->save();

			// Pour résoudre une faille de sécurité de notre super Laravel/Passport, à la génération d'un nouveau code, il faut révoker toutes les anciennes du même type
			Token::where('user_id', $code->user_id)->where('client_id', $code->client_id)->where('scopes', $code->scopes)->update(['revoked' => true]);
		}

		return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Session;
use Laravel\Passport\Client;
use Laravel\Passport\AuthCode;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
		if ($request->user() !== null) {
			$token = $request->user()->token();
			$client = Client::find($token->client_id);

			if ($client !== null && !$client->personal_access_client && !$client->password_client) {
				//$authCode = AuthCode::where('user_id', $token->user_id)->where('client_id', $token->client_id)->where('scopes', json_encode($token->scopes))->where('revoked', true)->first();
				$session = Session::find($token->session_id);

				if ($session === null || $session->user_id !== $request->user()->id)
					return response()->json(['message' => 'L\'utilisateur n\'est plus connecté'], 410);
			}
		}

		return $next($request);
    }
}

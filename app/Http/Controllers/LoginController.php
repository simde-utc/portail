<?php

namespace App\Http\Controllers;

use Route;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use App\Services\Auth\Cas;

class LoginController extends Controller
{
	use AuthenticatesUsers;

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		if (Auth::check())
			return $this->alreadyConnected();

		$services = config('auth.services');
		$auth = [];

		foreach ($services as $provider => $service)
			$auth[$provider] = [
				'name' => $service['name'],
				'description' => $service['description'],
				'url' => route('login.show', ['provider' => $provider]),
				'register_url' => $service['registrable'] ? route('register.show', ['provider' => $provider]) : null,
			];

		return response()->json($auth, 200);
	}

	/**
	 * Déconnection de l'utilisateur
	 */
	public function destroy(Request $request) {
		$token = $request->user()->token();
		$session_id = $token->session_id;
		$service = config("auth.services.".(\App\Models\Session::find($session_id)->auth_provider));
		$redirect = $service === null ? null : resolve($service['class'])->logout($request);

		if ($redirect === null) {
			// On le déconnecte uniquement lorsque le service a fini son travail
			\App\Models\Session::find($session_id)->update([
				'user_id' => null,
				'auth_provider' => null,
			]);

			return response()->json(['message' => 'Utilisateur déconnecté avec succès'], 202);
		}
		else
			return response()->json(['redirect' => route('logout')], 200);
	}
}

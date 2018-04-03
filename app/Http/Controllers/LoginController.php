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
			];

		return response()->json($auth, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $provider) {
		/*
			Sera utile pour l'authenification par mdp
		 */
	}

	/**
	 * Déconnection de l'utilisateur
	 */
	public function logout(Request $request) {
		$token = $request->user()->token();
		$service = config("auth.services.".Session::find($token->session_id)->auth_provider);

		if ($service === null) {
			if ($request->query('redirect', url()->previous()))
				$redirect = redirect($request->query('redirect', url()->previous()));
			else
				$redirect = redirect('home');
		}
		else
			$redirect = resolve($service['class'])->logout($request);

		// On le déconnecte uniquement lorsque le service a fini son travail
		\App\Models\Session::find(\Session::getId())->update(['auth_provider' => null]);
    	Auth::logout();

		return $redirect;
	}
}

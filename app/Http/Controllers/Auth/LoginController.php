<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use App\Services\Auth\Cas;


class LoginController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Affiche la vue de choix de méthode de login
	 */
	public function showLoginOptions() {
		return view('login.index');
	}

	/**
	 * Récupère la classe d'authentication $provider_class dans le service container de Laravel
	 * et applique le show login
	 */
	public function showLoginForm($provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login.show');
		else
			return resolve($provider_class)->showLoginForm();
	}

	/**
	 * Connection de l'utilisateur après passage par l'API
	 */
	public function login(Request $request, $provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login.show');
		else
			return resolve($provider_class)->login($request);
	}

	/**
	 * Déconnection de l'utilisateur
	 */
	public function logout() {
		$provider = config("auth.services.".Auth::user()->provider);

    Auth::logout();

    if ($provider === null || !$provider['redirect'])
			return redirect('home');
		else
			return resolve($provider['class'])->logout($request);      // Redirection vers la page choisie par le consommateur de l'API
	}
}

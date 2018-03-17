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

	public function __construct()	{
			$this->middleware('auth', ['only' => 'logout']);
	}

	/**
	 * Affiche la vue de choix de méthode de login
	 */
	public function index() {
		return view('login.index');
	}

	/**
	 * Récupère la classe d'authentication $provider_class dans le service container de Laravel
	 * et applique le show login
	 */
	public function show($provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login.show');
		else
			return resolve($provider_class)->show();
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
	public function logout(Request $request) {
		$service = config("auth.services.".Auth::user()->getCurrentAuth());

		if ($service === null) {
			if ($request->query('redirection'))
				$redirect = redirect($request->query('redirection'));
			else
				$redirect = redirect('home');
		}
		else
			$redirect = resolve($service['class'])->logout($request);

		// On le déconnecte uniquement lorsque le service a fini son travail
    Auth::logout();
		return $redirect;
	}
}

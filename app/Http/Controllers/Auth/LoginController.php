<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use App\Services\Auth\Cas;
use Laravel\Passport\Token;
use App\Models\Session;

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
		$this->middleware('guest', ['except' => 'logout']);
		$this->middleware('auth', ['only' => 'logout']);
	}

	/**
	 * Affiche la vue de choix de méthode de login
	 */
	public function index(Request $request) {
		$provider = $request->cookie('auth_provider');
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null || $request->query('see') === 'all')
			return view('login.index', ['redirect' => $request->query('redirect', url()->previous())]);
		else
			return redirect()->route('login.show', ['provider' => $provider, 'redirect' => $request->query('redirect', url()->previous())]);
	}

	/**
	 * Récupère la classe d'authentication $provider_class dans le service container de Laravel
	 * et applique le show login
	 */
	public function show(Request $request, $provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login', ['redirect' => $request->query('redirect', url()->previous())])->cookie('auth_provider', '', config('portail.cookie_lifetime'));
		else
			return resolve($provider_class)->show($request);
	}

	/**
	 * Connection de l'utilisateur après passage par l'API
	 */
	public function login(Request $request, $provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login.show', ['redirect' => $request->query('redirect', url()->previous())]);
		else {
			setcookie('auth_provider', $provider, config('portail.cookie_lifetime'));

			return resolve($provider_class)->login($request);
		}
	}

	/**
	 * Déconnection de l'utilisateur
	 */
	public function logout(Request $request) {
		$service = config("auth.services.".Session::find(\Session::getId())->auth_provider);

		if ($service === null) {
			if ($request->query('redirect', url()->previous()))
				$redirect = redirect($request->query('redirect', url()->previous()));
			else
				$redirect = redirect('home');
		}
		else
			$redirect = resolve($service['class'])->logout($request);

		// On le déconnecte uniquement lorsque le service a fini son travail
		Session::find(\Session::getId())->update(['auth_provider' => null]);
    	Auth::logout();

		return $redirect;
	}
}

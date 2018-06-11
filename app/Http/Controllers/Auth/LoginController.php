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
		$this->middleware('guest', ['except' => 'destroy']);
		$this->middleware('auth:web', ['only' => 'destroy']);
	}

	/**
	 * Affiche la vue de choix de méthode de login
	 */
	public function index(Request $request) {
		$provider = $request->cookie('auth_provider');
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null || $request->query('see') === 'all')
			return view('login.index');
		else
			return redirect()->route('login.show');
	}

	/**
	 * Connection de l'utilisateur après passage par l'API
	 */
	public function store(Request $request, string $provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login.show');
		else {
			return resolve($provider_class)->login($request)->cookie('auth_provider', '', config('portail.cookie_lifetime'));
		}
	}

	/**
	 * Récupère la classe d'authentication $provider_class dans le service container de Laravel
	 * et applique le show login
	 */
	public function show(Request $request, string $provider) {
		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->route('login')->cookie('auth_provider', '', config('portail.cookie_lifetime'));
		else
			return resolve($provider_class)->showLoginForm($request);
	}

	/**
	 * Actualisation du captcha
	 */
	public function update(Request $request) {
		return response()->json(['captcha' => captcha_img()]);
	}

	/**
	 * Déconnection de l'utilisateur
	 */
	public function destroy(Request $request, string $redirect = null) {
		$service = config("auth.services.".Session::find(\Session::getId())->auth_provider);
		$redirect = $service === null ? null : resolve($service['class'])->logout($request);

		if ($redirect === null) {
			$after_logout_redirection = $request->query('redirect', url()->previous());
			// Évite les redirections sur logout
			if ($after_logout_redirection && $after_logout_redirection !== $request->url())
				$redirect = redirect($after_logout_redirection);
			else
				$redirect = redirect('welcome');
		}

		// Ne pas oublier de détruire sa session
		\Session::flush();

		// On le déconnecte uniquement lorsque le service a fini son travail
		Auth::logout();

		return $redirect;
	}
}

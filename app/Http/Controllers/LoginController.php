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
			array_push($auth, [
				'name' => $provider,
				'text' => $service['text'],
				'url' => route('login.show').'/'.$provider,
			]);

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
	 * Display the specified resource.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Http\Response
	 */
	public function show($provider) {
		if (Auth::check())
			return $this->alreadyConnected();

		$provider_class = config("auth.services.$provider.class");

		if ($provider_class === null)
			return redirect()->action('LoginController@index');
		else
			return resolve($provider_class)->show();
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		// A voir si on actualise qq chose niveau
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request) {
		// TODO se déconnecter de tous les sites

		$provider = config("auth.services.".Auth::user()->getCurrentAuth());

		if ($provider !== null)
			$redirect = resolve($provider['class'])->logout($request);

		if ($redirect === null) {
			if ($request->query('redirection'))
				$redirect = redirect($request->query('redirection'));
			else
				$redirect = redirect('home');
		}

		// On le déconnecte uniquement lorsque le service a fini son travail
    	Auth::logout();

		return $redirect;
	}

	protected function alreadyConnected() {
		$user = Auth::user();

		return response()->json(['message' => 'Vous êtes déjà connecté sous '.$user->lastname.' '.$user->firstname], 409);
	}
}

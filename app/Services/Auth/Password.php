<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Password extends AuthService
{
	protected $name = 'password';

	public function __construct() {
		$this->config = config("auth.services." . $this->name);
	}

	public function showLoginForm() {
		return view('auth.login');
	}

	public function login(Request $request) {
		if ($request->input('email') && $request->input('password')) {
			$user = User::where('email', $request->input('email'))->first();

			if ($user !== null) {
				$userAuth = $user->password()->first();

				if ($userAuth !== null && Hash::check($request->input('password'), $userAuth->password))
					return $this->connect($user, $userAuth);
			}

			return $this->error(null, null);
		}
		else
			return $this->showLoginForm();
	}

	/*
	 * Redirige vers la bonne page en cas de succès
	 */
	protected function success($user, $userAuth) {
		$casAuth = $user->cas()->first();

		if ($casAuth !== null && $casAuth->active) {
			$casAuth->active = 0;
			$casAuth->save();

			return redirect('home')->withSuccess('Vous êtes maintenant considéré.e comme un.e Tremplin'); // TODO: taper sur Ginger pour désactiver ou non le compte CAS
		}
		else
			return redirect('home');
	}

	/*
	 * Redirige vers la bonne page en cas d'erreur
	 */
	protected function error($user, $userAuth) {
		return redirect()->route('login', ['provider' => $this->name])->withError('L\'adresse email et/ou le mot de passe est incorrecte');	// TODO
	}
}

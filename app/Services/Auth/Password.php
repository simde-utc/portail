<?php

namespace App\Services\Auth;

use Ginger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Password extends BaseAuth
{
	protected $name = 'password';

	public function __construct() {
		$this->config = config("auth.services.".$this->name);
	}

	public function login(Request $request) {
		if ($request->input('email') && $request->input('password')) {
			$user = User::where('email', $request->input('email'))->first();

			if ($user !== null) {
				$userAuth = $user->password()->first();

				if ($userAuth !== null && Hash::check($request->input('password'), $userAuth->password))
					return $this->connect($request, $user, $userAuth);
			}

			return $this->error($request, null, null, 'L\'adresse email et/ou le mot de passe est incorrecte');
		}
		else
			return $this->show($request);
	}

	public function register(Request $request) {
		$this->type = "register";

		return $this->create($request, [
			'email' => $request->input('email'),
			'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
		], [
			'password' => Hash::make($request->input('password')),
		]);
	}

	/*
	 * Redirige vers la bonne page en cas de succès
	 */
	protected function success(Request $request, $user = null, $userAuth = null, $message = null) {
		$casAuth = $user->cas;

		if ($casAuth !== null && $casAuth->is_active && !Ginger::userExists($casAuth->login)) { // Si l'utilisateur n'existe plus auprès de Ginger, on peut désactiver son compte
			$casAuth->is_active = 0;
			$casAuth->save();

			return parent::success($request, $user, $userAuth, 'Vous êtes maintenant considéré.e comme un.e Tremplin');
		}
		else
			return parent::success($request, $user, $userAuth, $message);
	}
}

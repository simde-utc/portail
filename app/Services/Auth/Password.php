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

			return $this->error($request, null, null, 'L\'adresse email et/ou le mot de passe est incorrect');
		}
		else
			return $this->show($request);
	}

	public function showRegisterForm(Request $request) {
		if ($request->has('newCaptcha'))
			return response()->json(['captcha' => captcha_img()]);
		else
			return parent::showRegisterForm($request);
	}

	public function register(Request $request) {
		$request->validate([
			'email' => ['required', 'email', 'regex:#.*(?<!utc\.fr|escom\.fr)$#', 'unique:users'],
			'firstname' => 'required|regex:#^[\pL\s\-]+$#u',
			'lastname' => 'required|regex:#^[\pL\s\-]+$#u',
			'password' => 'required|confirmed|regex:#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#',
			'birthdate' => 'required|date_format:Y-m-d|before_or_equal:'.\Carbon\Carbon::now()->subYears(16)->toDateString(),
			'captcha' => 'required|captcha'
		],
		[
			'email.unique' => 'L\'adresse email est déjà utilisée',
			'email.regex' => 'Il n\'est pas possible de s\'enregistrer avec une adresse email utc.fr ou escom.fr (Veuillez vous connecter via CAS-UTC)',
			'password.regex' => 'Le mot de passe doit avoir au moins: 8 caractères, une lettre en minuscule, une lettre en majuscule, un chiffre',
			'captcha.captcha' => 'Le Captcha est invalide',
		]);

		return $this->create($request, [
			'email' => $request->input('email'),
			'firstname' => $request->input('firstname'),
            'lastname' => $request->input('lastname'),
		], [
			'password' => Hash::make($request->input('password')),
		]);
	}


	/**
	 * Crée la connexion auth
	 */
	public function addAuth($user_id, array $info) {
		$info['password'] = Hash::make($info['password']);

		return parent::addAuth($user_id, $info);
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

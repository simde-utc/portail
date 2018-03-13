<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreferences;

abstract class BaseAuth
{
	/**
	 * !! Attributs à overrider
	 */
	protected $name, $config;

	/**
	 * Renvoie un lien vers le formulaire de login
	 */
	abstract public function showLoginForm();

	/**
	 * Callback pour récupérer les infos de l'API en GET et login de l'utilisateur
	 */
	abstract function login(Request $request);

	/**
	 * Callback pour se logout
	 */
	public function logout(Request $request) {
		return redirect('home');
	}

	/**
	 * Retrouve l'utilisateur via le modèle qui correspond au mode d'authentification
	 */
	protected function findUser($key, $value) {
		return resolve($this->config['model'])::where($key, $value)->first();
	}


	/**
	 * Crée l'utilisateur et son mode de connexion auth_{provider}
	 */
	protected function create(array $userInfo, array $authInfo) {
		// Création de l'utilisateur avec les informations minimales
		$user = $this->createUser($userInfo);

		// On crée le système d'authentification
		$userAuth = $this->createAuth($user->id, $authInfo);

		return $this->connect($user, $userAuth);
	}

	/**
	 * Met à jour les informations de l'utilsateur et de son mode de connexion auth_{provider}
	 */
	protected function update($id, array $userInfo = [], array $authInfo = []) {
		// Actualisation des informations
		$user = $this->updateUser($id, $userInfo);

		// On actualise le système d'authentification
		$userAuth = $this->updateAuth($id, $authInfo);

		return $this->connect($user, $userAuth);
	}

	/**
	 * Crée ou ajuste les infos de l'utilisateur et son mode de connexion auth_{provider}
	 */
	protected function updateOrCreate($key, $value, array $userInfo = [], array $authInfo = []) {
		// On cherche l'utilisateur
		$userAuth = $this->findUser($key, $value);

		if ($userAuth === null)
			return $this->create($userInfo, $authInfo); // Si inconnu, on le crée et on le connecte.
		else
			return $this->update($userAuth->user_id, $userInfo, $authInfo); // Si connu, on actualise ses infos et on le connecte.
	}


	/**
	 * Crée l'utilisateur User
	 */
	protected function createUser(array $info) {
		$user = User::updateOrCreate([
			'email' => $info['email']
		], [
		  'lastname' => $info['lastname'],
		  'firstname' => $info['firstname'],
		  'last_login_at' => new \DateTime(),
		]);

		// TODO Dans le cas où User n'aurait pas été créé

		// Ajout dans les préférences
		$userPreferences = UserPreferences::updateOrCreate([
		  'user_id' => $user->id,
		], [
		  'email' 	=> $user->email,
		]);

		// TODO Dans le cas où UserPreferences n'aurait pas été créé

		return $user;
	}

	/**
	 * Met à jour l'utilisateur User
	 */
	protected function updateUser($id, array $info = []) {
		$user = User::find($id);

		if ($user === null)
			return null;

		if ($info !== []) {
			$user->lastname = $info['lastname'];
			$user->firstname = $info['firstname'];
			$user->save();
		}

		return $user;
	}


	/**
	 * Crée la connexion auth
	 */
	protected function createAuth($id, array $info = []) {
		return resolve($this->config['model'])::updateOrCreate(array_merge($info, [
		  'user_id' => $id,
		], [
		  'last_login_at' => new \DateTime(),
		]));
	}

	/**
	 * Met à jour la connexion auth
	 */
	protected function updateAuth($id, $info = []) {
		$userAuth = resolve($this->config['model'])::find($id);

		foreach ($info as $key => $value)
		  $userAuth->$key = $value;

		$userAuth->save();

		return $userAuth;
	}

	/**
	 * Permet de se connecter
	 */
	protected function connect($user, $userAuth) {
		// Si tout est bon, on le connecte
		if ($user !== null && $userAuth !== null) {
			$user->timestamps = false;
			$user->last_login_at = new \DateTime();
			$user->save();

			$userAuth->timestamps = false;
			$userAuth->last_login_at = new \DateTime();
			$userAuth->save();

			Auth::login($user);

			return $this->success($user, $userAuth);
		}
		else
			return $this->error($user, $userAuth);
	}

	/*
	 * Redirige vers la bonne page en cas de succès
	 */
	protected function success($user, $userAuth) {
		return redirect('home');
	}

	/*
	 * Redirige vers la bonne page en cas d'erreur
	 */
	protected function error($user, $userAuth) {
		return redirect()->route('login.show')->withError('Il n\'a pas été possible de vous connecter');	// TODO
	}
}

<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreferences;

abstract class AuthService
{
	/**
	 * !! Attributs à overrider
	 */
	protected $name, $processURL, $config;

	/**
	 * !! Fonction à overrider
	 * Renvoie un lien vers le formulaire de login
	 */
	public function showLoginForm() { }

	/**
	 * !! Fonction à overrider
	 * Callback pour récupérer les infos de l'API en GET et login de l'utilisateur
	 */
	public function login(Request $request) { }

	/**
	 * !! Fonction à overrider
	 * Callback pour se logout
	 */
	public function logout() { }



	/**
	 * Retrouve l'utilisateur via le $model spécifié qui correspond au mode d'authentification
	 */
	protected function findUser($model, $key, $value) {
		return $model::where($key, $value)->first();
	}
	
	/**
	 * Crée l'utilisateur et son mode de connexion auth_...
	 */
	protected function create($model, array $user_infos, array $auth_infos = []) {
		// Création de l'utilisateur avec les informations minimales
		$user = $this->createUser($user_infos);
		if (!$user)	dd("ERREUR AuthService.php@create");	// TODO

		// On crée le système d'authentification
		$userAuth = $this->createAuth($model, $user->id, $auth_infos);

		// Si tout est bon, on le connecte
		if ($user !== null && $userAuth !== null)
			Auth::login($user);
	}

	/**
	 * Met à jour les informations de l'utilsateur et de son mode de connexion
	 */
	protected function update($model, $id, array $user_infos, array $auth_infos = []) {
		// Actualisation des informations
		$user = $this->updateUser($id, $user_infos);

		// On actualise le système d'authentification
		$userAuth = $this->updateAuth($model, $id, $auth_infos);

		// Si tout est bon, on le connecte
		if ($user !== null && $userAuth !== null)
			Auth::login($user);
	}



	/**
	 * Crée l'utilisateur User
	 */
	protected function createUser(array $infos) {
		$infos['last_login_at'] = new \DateTime();
		$user = User::create($infos);

		if (!$user)
			dd("ERREUR AuthService.php@createUser");		// TODO

		// Ajout dans les préférences
		$userPreferences = UserPreferences::create([
		  'user_id' => $user->id,
		  'email' 	=> $user->email,
		]);

		return $user;
	}

	/**
	 * Met à jour l'utilisateur User
	 */
	protected function updateUser($id, array $infos) {
		$user = User::find($id);
		if (!$user)
			dd("ERREUR AuthService.php@updateUser");		// TODO

		$user->lastname = $infos['lastname'];
		$user->firstname = $infos['firstname'];
		$user->timestamps = false;
		$user->last_login_at = new \DateTime();
		$user->save();

		return $user;
	}



	/**
	 * Crée la connexion auth
	 */
	protected function createAuth($model, $id, array $infos = []) {
		return $model::create(array_merge($infos, [
		  'user_id' => $id,
		  'last_login_at' => new \DateTime(),
		]));
	}

	/**
	 * Met à jour la connexion auth
	 */
	protected function updateAuth($model, $id, $infos = []) {
		$userAuth = $model::find($id);

		foreach ($infos as $key => $value)
		  $userAuth->$key = $value;

		$userAuth->save();

		$userAuth->timestamps = false;
		$userAuth->last_login_at = new \DateTime();
		$userAuth->save();

		return $userAuth;
	}

}

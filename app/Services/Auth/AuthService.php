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
	protected function create(array $userInfo, array $authInfo = []) {
		// Création de l'utilisateur avec les informations minimales
		$user = $this->createUser($userInfo);

		// On crée le système d'authentification
		$userAuth = $this->createAuth($user->id, $authInfo);

		return $this->connect($user, $userAuth);
	}

	/**
	 * Met à jour les informations de l'utilsateur et de son mode de connexion auth_{provider}
	 */
	protected function update($id, array $userInfo, array $authInfo = []) {
		// Actualisation des informations
		$user = $this->updateUser($id, $userInfo);

		// On actualise le système d'authentification
		$userAuth = $this->updateAuth($id, $authInfo);

		return $this->connect($user, $userAuth);
	}

	/**
	 * Crée ou ajuste les infos de l'utilisateur et son mode de connexion auth_{provider}
	 */
	protected function createOrUpdate($key, $value, array $userInfo, array $authInfo = []) {
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
	protected function createUser(array $infos) {
		$user = User::create([
			'email' => $infos['email'],
		  'lastname' => $infos['lastname'],
		  'firstname' => $infos['firstname'],
		  'last_login_at' => new \DateTime(),
		]);

		// TODO Dans le cas où User n'aurait pas été créé

		// Ajout dans les préférences
		$userPreferences = UserPreferences::create([
		  'user_id' => $user->id,
		  'email' 	=> $user->email,
		]);

		// TODO Dans le cas où UserPreferences n'aurait pas été créé

		return $user;
	}

	/**
	 * Met à jour l'utilisateur User
	 */
	protected function updateUser($id, array $infos) {
		$user = User::find($id);

		// TODO Dans le cas où User n'aurait pas été trouvé

		$user->lastname = $infos['lastname'];
		$user->firstname = $infos['firstname'];
		$user->save();

		$user->timestamps = false;
		$user->last_login_at = new \DateTime();
		$user->save();

		return $user;
	}


	/**
	 * Crée la connexion auth
	 */
	protected function createAuth($id, array $infos = []) {
		return resolve($this->config['model'])::create(array_merge($infos, [
		  'user_id' => $id,
		  'last_login_at' => new \DateTime(),
		]));
	}

	/**
	 * Met à jour la connexion auth
	 */
	protected function updateAuth($id, $infos = []) {
		$userAuth = resolve($this->config['model'])::find($id);

		foreach ($infos as $key => $value)
		  $userAuth->$key = $value;

		$userAuth->save();

		$userAuth->timestamps = false;
		$userAuth->last_login_at = new \DateTime();
		$userAuth->save();

		return $userAuth;
	}

	/**
	 * Permet de se connecter
	 */
	protected function connect($user, $userAuth) {
		// Si tout est bon, on le connecte
		if ($user !== null && $userAuth !== null) {
			// On ajoute les attributs fournis par auth
			$user->addAttributes($this->name, $userAuth);

			Auth::login($user, true);

			return redirect('home');
		}
		// TODO Dans le cas où ça n'aurait pas marché
	}
}

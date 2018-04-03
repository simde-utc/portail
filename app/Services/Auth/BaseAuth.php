<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Session;

abstract class BaseAuth
{
	/**
	 * !! Attributs à overrider
	 */
	protected $name, $config;

	/**
	 * Renvoie un lien vers le formulaire de login
	 */
	public function show(Request $request) {
		return view('auth.'.$this->name.'.login', ['redirect' => $request->query('redirect', url()->previous())]);
	}

	/**
	 * Callback pour récupérer les infos de l'API en GET et login de l'utilisateur
	 */
	abstract function login(Request $request);

	/**
	 * Callback pour se logout
	 */
	public function logout(Request $request) {
		if ($request->query('redirect'))
			return redirect()->$request->query('redirect');
		else
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
	protected function create(Request $request, array $userInfo, array $authInfo) {
		// Création de l'utilisateur avec les informations minimales
		$user = $this->createUser($userInfo);

		// On crée le système d'authentification
		$userAuth = $this->createAuth($user->id, $authInfo);

		return $this->connect($request, $user, $userAuth);
	}

	/**
	 * Met à jour les informations de l'utilsateur et de son mode de connexion auth_{provider}
	 */
	protected function update(Request $request, $id, array $userInfo = [], array $authInfo = []) {
		// Actualisation des informations
		$user = $this->updateUser($id, $userInfo);

		// On actualise le système d'authentification
		$userAuth = $this->updateAuth($id, $authInfo);

		return $this->connect($request, $user, $userAuth);
	}

	/**
	 * Crée ou ajuste les infos de l'utilisateur et son mode de connexion auth_{provider}
	 */
	protected function updateOrCreate(Request $request, $key, $value, array $userInfo = [], array $authInfo = []) {
		// On cherche l'utilisateur
		$userAuth = $this->findUser($key, $value);

		if ($userAuth === null)
			return $this->create($request, $userInfo, $authInfo); // Si inconnu, on le crée et on le connecte.
		else
			return $this->update($request, $userAuth->user_id, $userInfo, $authInfo); // Si connu, on actualise ses infos et on le connecte.
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
		$userPreferences = UserPreference::updateOrCreate([
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
	protected function connect(Request $request, $user, $userAuth) {
		// Si tout est bon, on le connecte
		if ($user !== null && $userAuth !== null) {
			$user->timestamps = false;
			$user->last_login_at = new \DateTime();
			$user->save();

			$userAuth->timestamps = false;
			$userAuth->last_login_at = new \DateTime();
			$userAuth->save();

			Auth::login($user);
			Session::updateOrCreate(
				[
					'id' => \Session::getId(),
				],
				[
					'auth_provider' => $this->name,
				]
			);

			return $this->success($request, $user, $userAuth);
		}
		else
			return $this->error($request, $user, $userAuth);
	}

	/*
	 * Redirige vers la bonne page en cas de succès
	 */
	protected function success(Request $request, $user = null, $userAuth = null, $message = null) {
		if ($message === null)
			return redirect($request->query('redirect', url()->previous()));
		else
			return redirect($request->query('redirect', url()->previous()))->withSuccess($message);
	}

	/*
	 * Redirige vers la bonne page en cas d'erreur
	 */
	protected function error(Request $request, $user = null, $userAuth = null, $message = null) {
		if ($message === null)
			return redirect()->route('login.show', ['provider' => $this->name, 'redirect' => $request->query('redirect', url()->previous())])->withError('Il n\'a pas été possible de vous connecter');
		else
			return redirect()->route('login.show', ['provider' => $this->name, 'redirect' => $request->query('redirect', url()->previous())])->withError($message);
	}
}

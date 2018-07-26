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
	public function showLoginForm(Request $request) {
		return view('auth.'.$this->name.'.login', ['provider' => $this->name, 'redirect' => $request->query('redirect', url()->previous())]);
	}

	/**
	 * Renvoie un lien vers le formulaire d'enregistrement
	 */
	public function showRegisterForm(Request $request) {
		if ($this->config['registrable'])
			return view('auth.'.$this->name.'.register', ['provider' => $this->name, 'redirect' => $request->query('redirect', url()->previous())]);
		else
			return redirect()->route('register.show', ['redirect' => $request->query('redirect', url()->previous())])->cookie('auth_provider', '', config('portail.cookie_lifetime'));
	}

	/**
	 * Callback pour récupérer les infos de l'API en GET et login de l'utilisateur
	 */
	abstract function login(Request $request);
	abstract function register(Request $request);

	/**
	 * Callback pour se logout
	 */
	public function logout(Request $request) {
		return null;
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
		try {
			$user = $this->createUser($userInfo);
		}
		catch (\Exception $e) {
			return $this->error($request, null, null, 'Cette adresse mail est déjà utilisée');
		}

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

		if ($userAuth === null) {
			$user = isset($userInfo['email']) ? User::where('email', $userInfo['email'])->first() : null;

			if ($user === null) {
				try {
					return $this->create($request, $userInfo, $authInfo); // Si inconnu, on le crée et on le connecte.
				}
				catch (\Exception $e) {
					return $this->error($request, null, null, 'Cette adresse mail est déjà utilisé mais n\'est pas relié au bon compte');
				}
			}
			else {
				$user = $this->updateUser($user->id, $userInfo);
				$userAuth = $this->createAuth($user->id, $authInfo);

				return $this->connect($request, $user, $userAuth);
			}

		}
		else
			return $this->update($request, $userAuth->user_id, $userInfo, $authInfo); // Si connu, on actualise ses infos et on le connecte.
	}


	/**
	 * Crée l'utilisateur User
	 */
	protected function createUser(array $info) {
		$user = User::create([
			'email' => $info['email'],
			'lastname' => $info['lastname'],
			'firstname' => $info['firstname'],
			'is_active' => true,
		]);

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
	 * Création ou mis à jour de l'utilisateur User
	 */
	protected function updateOrCreateUser(array $info) {
		$user = User::findByEmail($info['email']);

		if ($user)
			return $this->updateUser($user->id, $info);
		else
			return $this->createUser($info);
	}


	/**
	 * Crée la connexion auth
	 */
	public function addAuth($user_id, array $info) {
		return resolve($this->config['model'])::create(array_merge($info, [
			'user_id' => $user_id
		]));
	}

	/**
	 * Crée la connexion auth
	 */
	protected function createAuth($id, array $info = []) {
		return resolve($this->config['model'])::updateOrCreate([
			'user_id' => $id,
		], array_merge($info, [
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
		if ($user && $userAuth) {
			if (!$user->is_active)
				return $this->error($request, $user, $userAuth, 'Ce compte a été désactivé');

			$user->timestamps = false;
			$user->last_login_at = new \DateTime();
			$user->save();

			$userAuth->timestamps = false;
			$userAuth->last_login_at = new \DateTime();
			$userAuth->save();

			Auth::guard('web')->login($user);
			Session::updateOrCreate(['id' => \Session::getId()], ['auth_provider' => $this->name]);

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
			return redirect(\Session::get('url.intended', '/'));
		else
			return redirect(\Session::get('url.intended', '/'))->withSuccess($message);
	}

	/*
	 * Redirige vers la bonne page en cas d'erreur
	 */
	protected function error(Request $request, $user = null, $userAuth = null, $message = null) {
		if ($message === null)
			return redirect()->route('login.show', ['provider' => $this->name])->withError('Il n\'a pas été possible de vous connecter')->withInput();
		else
			return redirect()->route('login.show', ['provider' => $this->name])->withError($message)->withInput();
	}
}

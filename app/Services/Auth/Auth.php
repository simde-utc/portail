<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth as Authentification;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreferences;

abstract class Auth
{
	protected $model = User::class;

	protected function findUser($key, $value, $use_specific_model = false) {
		if ($use_specific_model) {
			$model = self::$model;
			return $model::where($key, $value)->first();
		} else {
			return User::where($key, $value)->first();
		}
	}

	protected function create($email, $lastname, $firstname, $infos = []) {
		// Création de l'utilisateur avec les informations minimales
		$user = self::createUser($email, $lastname, $firstname);

		// On crée le système d'authentification
		$auth = self::createAuth($user->id, $infos);

		// Si tout est bon, on le connecte
		if ($user !== null && $auth !== null)
			self::connect($user);
	}

	protected function update($lastname, $firstname, $infos = []) {
	  // Actualisation des informations
	  $user = self::updateUser($lastname, $firstname);

	  // On actualise le système d'authentification
	  $auth = self::updateAuth($infos);

	  // Si tout est bon, on le connecte
	  if ($user !== null && $auth !== null)
		$this->connect();
	}

	protected function createUser($email, $lastname, $firstname) {
	  $user = User::create([
		'email' => $email,
		'lastname' => $lastname,
		'firstname' => $firstname,
		'last_login_at' => new \DateTime()
	  ]);

	  // Ajout dans les préférences
	  $userPreferences = UserPreferences::create([
		'user_id' => $this->getUserId(),
		'email' => $email,
	  ]);

	  return $user;
	}

	protected static function updateUser($id, $lastname, $firstname) {
	  $user = User::find($id);
	  $user->lastname = $lastname;
	  $user->firstname = $firstname;
	  $user->save();

	  $user->timestamps = false;
	  $user->last_login_at = new \DateTime();
	  $user->save();

	  return $user;
	}

	protected static function createAuth($id, $infos = []) {
	  $model = self::model;

	  return $model::create(array_merge($infos, [
		'user_id' => $id,
		'last_login_at' => new \DateTime(),
	  ]));
	}

	protected static function updateAuth($id, $infos = []) {
	  $auth = (static::model)::find($id);
	  foreach ($infos as $key => $value)
		$auth->$key = $value;

	  $auth->save();

	  $auth->timestamps = false;
	  $auth->last_login_at = new \DateTime();
	  $auth->save();
	}

	protected static function connect($user) {
	  Authentification::login($user);
	}
}

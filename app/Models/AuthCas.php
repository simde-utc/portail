<?php

namespace App\Models;

class AuthCas extends Auth // TODO must
{
	protected $fillable = [
	 	'user_id', 'login', 'email', 'last_login_at', 'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean', // Si on se connecte via passsword, on désactive tout ce qui est relié au CAS car on suppose qu'il n'est plus étudiant
	];

	public static function findByEmail($email) {
		return (new static)->where('email', $email)->first();
	}

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function getUserByIdentifiant($login) {
		$auth = $this->where('login', $login)->first();

		if ($auth)
			return $auth->user;
		else
			return null;
    }

	public function isPasswordCorrect($password) {
		// Intéraction avec le cas..
		return false;
	}
}

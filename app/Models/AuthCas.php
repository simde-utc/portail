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
		else {
			$ginger = \Ginger::user($login);

			if ($ginger->exists()) {
				$user = User::create([
					'email' => $ginger->getEmail(),
					'firstname' => $ginger->getFirstname(),
					'lastname' => $ginger->getLastname(),
					'is_active' => false,
				]);

				self::create([
					'user_id' => $user_id,
					'email' => $ginger->getEmail(),
				]);

				return $user;
			}
		}

		return null;
    }

	public function isPasswordCorrect($password) {
		$curl = \Curl::to(config('portail.cas.url').'v1/tickets')
			->withData([
				'username' => $this->login,
				'password' => $password
			])
			->returnResponseObject();

		if (strpos($_SERVER['HTTP_HOST'], 'utc.fr'))
			$curl = $curl->withProxy('proxyweb.utc.fr', 3128);

		$connected = $curl->post()->status === 201;

		if ($connected) {
			$this->user()->update([
				'is_active' => true
			]);
		}

		return $connected;
	}
}

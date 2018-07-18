<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;

class AuthPassword extends Auth
{
	protected $fillable = [
	 	'user_id', 'password', 'last_login_at',
	];

	protected $hidden = [
		'password',
	];

	public function hideData(array $params = []): Model {
		return $this; // TODO
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function getUserByIdentifiant($email) {
		return User::where('email', $email)->first();
    }

	public function isPasswordCorrect($password) {
		return Hash::check($password, $this->password);
	}
}

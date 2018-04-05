<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthCas extends Model
{
	public $incrementing = false;			// L'id n'est pas autoincrementé
	protected $primaryKey = 'user_id';

	protected $fillable = [
	 	'user_id', 'login', 'email', 'last_login_at', 'is_active',
	];

	protected $casts = [
		'is_active' => 'boolean', // Si on se connecte via passsword, on désactive tout ce qui est relié au CAS car on suppose qu'il n'est plus étudiant
	];

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function getUserByIdentifiant($login) {
		$auth = $this->where('login', $login)->first();

		if ($auth)
			return User::find($auth->user_id);
		else
			return null;
    }

	public function isPasswordCorrect($password) {
		// Intéraction avec le cas..
		return false;
	}
}

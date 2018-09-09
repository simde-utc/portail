<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use App\Traits\Model\HasHiddenData;
use NastuzziSamy\Laravel\Traits\HasSelection;


class AuthPassword extends Auth // TODO must
{
    use HasHiddenData, HasSelection;

    public $incrementing = false;

	protected $fillable = [
	 	'user_id', 'password', 'last_login_at',
	];

	protected $hidden = [
		'password',
	];

	protected $must = [
		'user_id',
	];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function getUserByIdentifiant($email) {
		return User::where('email', $email)->first();
    }

	public function isPasswordCorrect($password) {
		return Hash::check($password, $this->password);
	}
}

<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	use HasRoles;

	protected $fillable = [
		'firstname', 'lastname', 'email', 'last_login_at',
	];

	protected $hidden = [
		'remember_token',
	];

	public function cas() {
		return $this->hasOne('App\AuthCas');
	}
	public function password() {
		return $this->hasOne('App\AuthPassword');
	}

	public function scopeUtc($query) {
		return $query->has('auth_cas');
	}

}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
		return $this->hasOne('App\Models\AuthCas');
	}
	public function password() {
		return $this->hasOne('App\Models\AuthPassword');
	}

	public function scopeUtc($query) {
		return $query->has('auth_cas');
	}

	public function getProvider() {
		return Session::get('provider');
	}

	public function getAuth() {
		return Session::get('auth');
	}

	public function addAttributes($provider, $auth) {
		Session::set('provider', $provider);
		Session::set('auth', $auth);
	}
}

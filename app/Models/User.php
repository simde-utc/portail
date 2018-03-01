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

}

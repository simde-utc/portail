<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;

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

	public function getCurrentAuth() {
		$services = config('auth.services');

		foreach ($services as $service => $serviceInfo) {
			if (method_exists($this, $service) && $this->$service()->exists())
				return $service;
		}

		return null;
	}

	public function assos() {
		return $this->belongsToMany('App\Asso', 'asso_user');
	}

	public function contact() {
		// hasOne
	}
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;

class User extends Authenticatable
{
	use HasApiTokens, Notifiable, HasRoles;

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

	public function assoMember() {
		return $this->hasMany('App\Models\AssoMember');
	}

	public function assos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members');
	}

	public function currentAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->where('semester_id', Semester::getThisSemester()->id);
	}

	public function groups() {
		return $this->belongsToMany('App\Models\Group', 'groups_members');
	}

	public function currentGroups() {
		return $this->belongsToMany('App\Models\Group', 'groups_members')->where('is_active', 1);
	}

	public function ownGroups() {
		return $this->hasMany('App\Models\Group');
	}

	public function contact() {
        return $this->hasMany('App\Models\UserContact', 'contacts_users');
    }

    public function events() {
    	return $this->belongsToMany('App\Models\Event');
    }

	public function findForPassport($username) {
		$providers = config('auth.services');

		foreach ($providers as $provider) {
			$model = $provider['model'];

			if (method_exists($model, 'getUserByIdentifiant'))
				$user = (new $model)->getUserByIdentifiant($username);

			if ($user)
				return $user;
		}

		return null;
    }

	public function validateForPassportPasswordGrant($password) {
		$providers = config('auth.services');

		foreach ($providers as $name => $provider) {
			$auth = $this->$name;

			if ($auth) {
				if (method_exists($auth, 'isPasswordCorrect')) {
					if ($auth->isPasswordCorrect($password))
						return true;
				}
			}
		}

		return false;
	}
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasRoles;
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

	public static function findByEmail($email) {
		return static::where('email', $email)->first();
	}

	public static function getUsers($users) {
		if (is_array($users))
			return static::whereIn('id', $users)->orWhereIn('email', $users)->get();
		else if ($users instanceof \Illuminate\Database\Eloquent\Model)
			return collect($users);
		else
			return $users;
	}

	public function cas() {
		return $this->hasOne('App\Models\AuthCas');
	}
	public function password() {
		return $this->hasOne('App\Models\AuthPassword');
	}

	public function assos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members');
	}

	public function currentAssos() {
		return $this->assos()->where('semester_id', Semester::getThisSemester()->id);
	}

	public function followedAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->whereNull('role_id');
	}

	public function currentFollowedAssos() {
		return $this->joinedAssos()->where('semester_id', Semester::getThisSemester()->id);
	}

	public function joinedAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->whereNotNull('validated_by');
	}

	public function currentJoinedAssos() {
		return $this->joiningAssos()->where('semester_id', Semester::getThisSemester()->id);
	}

	public function joiningAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->whereNull('validated_by');
	}

	public function currentJoiningAssos() {
		return $this->joiningAssos()->where('semester_id', Semester::getThisSemester()->id);
	}

	public function groups() {
		return $this->belongsToMany('App\Models\Group', 'groups_members');
	}

	public function currentGroups() {
		return $this->groups()->where('is_active', 1);
	}

	public function ownGroups() {
		return $this->hasMany('App\Models\Group');
	}

	public function contact() {
        return $this->hasMany('App\Models\UserContact', 'users_contacts');
    }

    public function events() {
    	return $this->belongsToMany('App\Models\Event');
    }

	/**
	 * Fonctions permettant de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification
	 *
	 * @param string $username
	 * @return null|Illuminate\Database\Eloquent\Model
	 */
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

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Cog\Contracts\Ownership\CanBeOwner;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveContacts;
use App\Interfaces\Model\CanHaveEvents;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Traits\Model\HasRoles;
use App\Traits\Model\HasHiddenData;
use NastuzziSamy\Laravel\Traits\HasSelection;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\UserPreference;
use App\Models\UserDetail;
use App\Http\Requests\ContactRequest;
use App\Exceptions\PortailException;

class User extends Authenticatable implements CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents
{
	use HasHiddenData, HasSelection, HasApiTokens, Notifiable, HasRoles;

    public static function boot() {
        static::created(function ($model) {
			// Ajout dans les préférences
			UserPreference::create([
				'user_id' => $model->id,
				'key' => 'CONTACT_TO_USE',
				'value'   => [
					'EMAIL'
				],
			]);

			UserPreference::create([
				'user_id' => $model->id,
				'key' => 'CONTACT_EMAIL',
				'value'   => $model->email,
			]);
        });
    }

	protected $fillable = [
		'firstname', 'lastname', 'email', 'is_active', 'last_login_at',
	];

	protected $casts = [
		'is_active' => 'boolean',
	];

	protected $appends = [
		'name',
	];

	protected $hidden = [
		'remember_token',
	];

	public $types = [
		'admin', 'contributorBde', 'cas', 'password', 'active',
	];

	protected $selection = [
		'order' => 'oldest',
		'paginate' => null,
	];

	public function getNameAttribute() {
		return $this->firstname.' '.strtoupper($this->lastname);
	}

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

	public function ban() {
		return $this->update([
			'is_active' => false
		]);
	}

	public function unban() {
		return $this->update([
			'is_active' => true
		]);
	}

	public function type() {
		foreach ($this->types as $type) {
			$method = 'is'.ucfirst($type);

	        if (method_exists($this, $method) && $this->$method())
	            return $type;
		}

		return null;
	}

	public function isActive() {
        return $this->is_active;
    }

    public function isCas() {
		$cas = $this->cas;

        return $cas && $cas->where('is_active', true)->exists();
    }

    public function isPassword() {
		return $this->password()->exists();
    }

    public function isContributorBde() {
		try {
	        return $this->details()->valueOf('isContributorBde');
		} catch (PortailException $e) {
			return null;
		}
    }

    public function isAdmin() {
        return $this->hasOneRole(config('portail.roles.admin.users'));
    }

	public function cas() {
		return $this->hasOne('App\Models\AuthCas');
	}
	public function password() {
		return $this->hasOne('App\Models\AuthPassword');
	}

	public function sessions() {
		return $this->hasMany(Session::class);
	}

	public function details() {
		return $this->hasMany(UserDetail::class);
	}

	public function preferences() {
		return $this->hasMany(UserPreference::class);
	}

	public function assos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->withPivot('semester_id', 'role_id', 'validated_by');
	}

	public function currentAssos() {
		return $this->assos()->where('semester_id', Semester::getThisSemester()->id)->whereNotNull('role_id');
	}

	public function joinedAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->whereNotNull('validated_by')->whereNotNull('role_id')->withPivot('semester_id', 'role_id', 'validated_by');
	}

	public function currentJoinedAssos() {
		return $this->joinedAssos()->where('semester_id', Semester::getThisSemester()->id);
	}

	public function joiningAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->whereNull('validated_by')->whereNotNull('role_id')->withPivot('semester_id', 'role_id', 'validated_by');
	}

	public function currentJoiningAssos() {
		return $this->joiningAssos()->where('semester_id', Semester::getThisSemester()->id);
	}

	public function followedAssos() {
		return $this->belongsToMany('App\Models\Asso', 'assos_members')->whereNull('role_id')->withPivot('semester_id', 'role_id', 'validated_by');
	}

	public function currentFollowedAssos() {
		return $this->followedAssos()->where('semester_id', Semester::getThisSemester()->id);
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

    public function followedCalendars() {
    	return $this->belongsToMany(Calendar::class, 'calendars_followers')->withTimestamps();
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

	// Par défaut, un role n'est pas supprimable s'il a déjà été assigné
    // Mais on permet sa suppression s'il est assigné à un seul groupe
	public function isRoleForIdDeletable($role, $id) {
		return true;
	}

	public function contacts() {
		return $this->morphMany(Contact::class, 'owned_by');
	}

	public function isContactAccessibleBy(int $user_id): bool {
		return $this->id === $user_id;
	}

	public function isContactManageableBy(int $user_id): bool {
		return $this->id === $user_id;
	}

    public function calendars() {
    	return $this->morphMany(Calendar::class, 'owned_by');
    }

	public function isCalendarAccessibleBy(int $user_id): bool {
		return $this->id === $user_id;
	}

	public function isCalendarManageableBy(int $user_id): bool {
		return $this->id === $user_id;
	}

    public function events() {
    	return $this->morphMany(Event::class, 'owned_by');
    }

	public function isEventAccessibleBy(int $user_id): bool {
		return $this->id === $user_id;
	}

	public function isEventManageableBy(int $user_id): bool {
		return $this->id === $user_id;
	}
}

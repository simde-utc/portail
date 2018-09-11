<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Interfaces\Model\CanBeNotifiable;
use Cog\Contracts\Ownership\CanBeOwner;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveContacts;
use App\Interfaces\Model\CanHaveEvents;
use App\Interfaces\Model\CanHaveRoles;
use App\Interfaces\Model\CanHavePermissions;
use App\Interfaces\Model\CanComment;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Traits\Model\HasRoles;
use App\Traits\Model\HasHiddenData;
use App\Traits\Model\HasUuid;
use NastuzziSamy\Laravel\Traits\HasSelection;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\UserPreference;
use App\Models\UserDetail;
use App\Http\Requests\ContactRequest;
use App\Exceptions\PortailException;
use App\Notifications\User\UserCreation;
use App\Notifications\User\UserDesactivation;
use App\Notifications\User\UserModification;
use App\Notifications\User\UserDeletion;

class User extends Authenticatable implements CanBeNotifiable, CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents, CanHaveRoles, CanHavePermissions, CanComment
{
	use HasHiddenData, HasSelection, HasApiTokens, Notifiable, HasRoles, HasUuid {
		HasHiddenData::hideData as protected hideDataFromTrait;
	}

    public static function boot() {
		parent::boot();

        static::created(function ($model) {
			// Ajout dans les préférences
			$model->preferences()->create([
				'key' => 'NOTIFICATION_CHANNELS',
				'value' => [
					'mail', 'database', 'push'
				],
			]);

			$model->preferences()->create([
				'key' => 'CONTACT_EMAIL',
				'value' => $model->isActive() ? $model->email : null,
			]);

			$model->preferences()->create([
				'key' => 'NOTIFICATION_EMAIL_AVOID',
				'value' => [],
			]);

			$model->preferences()->create([
				'key' => 'NOTIFICATION_PUSH_AVOID',
				'value' => [],
			]);

			if ($model->isActive())
				$model->notify(new UserCreation());
        });

        static::updated(function ($model) {
			// Modfication des préférences
			if ($model->preferences()->valueOf('CONTACT_EMAIL') === $model->getOriginal('email')) {
				$model->preferences()->key('CONTACT_EMAIL')->update([
					'value' => $model->is_active ? $model->email : null,
				]);
			}

			if ((bool) $model->getOriginal('is_active') !== (bool) $model->getAttribute('is_active')) {
				$model->notify($model->isActive() ? new UserCreation() : new UserDesactivation());
			}

			$edited = [];

			if ($model->getOriginal('email') !== $model->getAttribute('email'))
				$edited['Adresse email'] = $model->email;

			if ($model->getOriginal('lastname') !== $model->getAttribute('lastname'))
				$edited['Nom'] = $model->lastname;

			if ($model->getOriginal('firstname') !== $model->getAttribute('firstname'))
				$edited['Prénom'] = $model->firstname;

			if (count($edited) > 0)
				$model->notify(new UserModification($edited));
        });

		static::deleting(function ($model) {
			$model->notify(new UserDeletion());
		});
    }

	public $incrementing = false;

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

	protected $must = [
		'me'
	];

	protected $types = [
		'admin', 'contributorBde', 'casConfirmed', 'cas', 'password', 'active',
	];

	protected $selection = [
		'order' => 'oldest',
		'paginate' => null,
		'filter' => [],
	];

	public function getMeAttribute() {
		return \Auth::id() === $this->id;
	}

	public function getNameAttribute() {
		if ($this->isActive())
			return $this->firstname.' '.strtoupper($this->lastname);
		else
			return 'Compte invité';
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

	public function notificationChannels(string $notificationType): array {
		$channels = $this->preferences()->valueOf('NOTIFICATION_CHANNELS');

		if (in_array($notificationType, $this->preferences()->valueOf('NOTIFICATION_EMAIL_AVOID')) && ($key = array_search('mail', $channels)) !== false)
			unset($channels[$key]);

		if (in_array($notificationType, $this->preferences()->valueOf('NOTIFICATION_PUSH_AVOID')) && ($key = array_search('push', $channels)) !== false)
    		unset($channels[$key]);

		if (!$this->isActive() && ($key = array_search('mail', $channels)) !== false)
			unset($channels[$key]);

		if (!$this->isApp() && ($key = array_search('push', $channels)) !== false)
    		unset($channels[$key]);

		return $channels;
	}

	public function routeNotificationForMail($notification) {
        return $this->preferences()->keyExistsInDB('CONTACT_EMAIL') ? $this->preferences()->valueOf('CONTACT_EMAIL') : null;
    }

		public function getTypes() {
			return $this->types;
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
    return $this->is_active === null || ((bool) $this->is_active) === true;
  }

  public function isCas() {
		$cas = $this->cas()->first();

    return $cas && $cas->is_active;
  }

  public function isCasConfirmed() {
		$cas = $this->cas()->first();

    return $cas && $cas->is_confirmed;
  }

  public function isPassword() {
		return $this->password()->exists();
  }

  public function isApp() {
		return $this->app()->exists();
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
		return $this->hasOne(AuthCas::class);
	}
	public function password() {
		return $this->hasOne(AuthPassword::class);
	}
	public function app() {
		return $this->hasMany(AuthApp::class);
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

	public function notifications() {
		return $this->morphMany(Notification::class, 'notifiable');
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

	public function followedServices() {
		return $this->belongsToMany(Service::class, 'services_followers')->withTimestamps();
	}

    public function comments() {
		return $this->hasMany('App\Models\Comment');
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

	public function isRoleAccessibleBy(string $user_id): bool {
		if ($this->id)
			return $this->id === $user_id;
		else
			return true;
	}

	public function isRoleManageableBy(string $user_id): bool {
		if ($this->id)
			return $this->id === $user_id;
		else
			return $this->hasOnePermission('role');
	}

	public function isPermissionAccessibleBy(string $user_id): bool {
		if ($this->id)
			return $this->id === $user_id;
		else
			return true;
	}

	public function isPermissionManageableBy(string $user_id): bool {
		if ($this->id)
			return $this->id === $user_id;
		else
			return $this->hasOnePermission('permission');
	}

	public function contacts() {
		return $this->morphMany(Contact::class, 'owned_by');
	}

	public function isContactAccessibleBy(string $user_id): bool {
		return $this->id === $user_id;
	}

	public function isContactManageableBy(string $user_id): bool {
		return $this->id === $user_id;
	}

    public function calendars() {
    	return $this->morphMany(Calendar::class, 'owned_by');
    }

	public function isCalendarAccessibleBy(string $user_id): bool {
		return $this->id === $user_id;
	}

	public function isCalendarManageableBy(string $user_id): bool {
		return $this->id === $user_id;
	}

    public function events() {
    	return $this->morphMany(Event::class, 'owned_by');
    }

	public function isEventAccessibleBy(string $user_id): bool {
		return $this->id === $user_id;
	}

	public function isEventManageableBy(string $user_id): bool {
		return $this->id === $user_id;
	}

	// On ne peut bien sûr pas écrire au nom de quelqu'un d'autre
	public function isCommentWritableBy(string $user_id): bool {
		return $user_id === $this->id;
	}

	public function isCommentEditableBy(string $user_id): bool {
		return $this->isCommentWritableBy($user_id);
	}

	public function isCommentDeletableBy(string $user_id): bool {
		return $this->isCommentEditableBy($user_id);
	}
}

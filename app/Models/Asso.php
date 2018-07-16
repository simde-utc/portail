<?php

namespace App\Models;

use Cog\Contracts\Ownership\CanBeOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\HasMembers;
use \App\Traits\HasStages;
use App\Interfaces\CanHaveContacts;
use App\Interfaces\CanHaveEvents;
use App\Interfaces\CanHaveCalendars;

class Asso extends Model implements CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents
{
	use SoftDeletes, HasStages, HasMembers {
		HasMembers::members as membersAndFollowers;
		HasMembers::currentMembers as currentMembersAndFollowers;
		HasMembers::joiners as protected joinersFromHasMembers;
		HasMembers::currentJoiners as currentJoinersFromHasMembers;
		HasMembers::getUserRoles as getUsersRolesInThisAssociation;
	}

    public static function boot() {
        static::created(function ($model) {
			// On crée automatiquement des moyens de contacts !
			Contact::create([
				'name' => 'Adresse email',
				'value' => $model->login.'@assos.utc.fr',
				'contact_type_id' => ContactType::where('name', 'Adresse email')->first()->id,
				'visibility_id' => Visibility::findByType('public')->id,
			])->changeOwnerTo($model)->save();

			Contact::create([
				'name' => 'Site Web',
				'value' => 'https://assos.utc.fr/'.$model->login.'/',
				'contact_type_id' => ContactType::where('name', 'Url')->first()->id,
				'visibility_id' => Visibility::findByType('public')->id,
			])->changeOwnerTo($model)->save();
        });
    }

	protected $roleRelationTable = 'assos_members';

	protected $fillable = [
		'name', 'shortname', 'login', 'description', 'type_asso_id', 'parent_id',
	];

	public function type() {
		return $this->belongsTo(AssoType::class, 'type_asso_id');
	}

	public function rooms() {
		return $this->hasMany(Room::class);
	}

	public function reservations() {
		return $this->hasMany(Reservation::class);
	}

	public function articles() {
		return $this->belongsToMany(Article::class, 'assos_articles');
	}

	public function collaboratedArticles(){
		return $this->belongsToMany('App\Models\Article', 'articles_collaborators');
	}

	public function parent() {
	    return $this->hasOne(Asso::class, 'parent_id');
    }

	public function children() {
		return $this->hasMany(Asso::class, 'parent_id', 'id');
    }

	public function members() {
		return $this->membersAndFollowers()->wherePivot('role_id', '!=', null);
	}

	public function currentMembers() {
		return $this->currentMembersAndFollowers()->wherePivot('role_id', '!=', null);
	}

	public function joiners() {
		return $this->joinersFromHasMembers()->wherePivot('role_id', '!=', null);
	}

	public function currentJoiners() {
		return $this->currentJoinersFromHasMembers()->wherePivot('role_id', '!=', null);
	}

	public function followers() {
		return $this->membersAndFollowers()->wherePivot('role_id', null);
	}

	public function currentFollowers() {
		return $this->currentMembersAndFollowers()->wherePivot('role_id', null);
	}

	public function getUserRoles(int $user_id = null, int $semester_id = null) {
		$parent_id = $this->parent_id;
		$roles = $this->getUsersRolesInThisAssociation($user_id, $semester_id);

		while ($parent_id) {
			$asso = static::find($parent_id);

			foreach ($asso->getUsersRolesInThisAssociation($user_id, $semester_id) as $role) {
				$roles->push($role);

				$roles = $roles->merge($role->allChildren());
				$role->makeHidden('children');
			}

			$parent_id = $asso->parent_id;
		}

		return $roles->unique('id');
	}

	public function getLastUserWithRole($role) {
		return $this->members()->wherePivot('role_id', Role::getRole($role)->id)->orderBy('semester_id', 'DESC')->first();
	}

	public function hide() {
		$this->makeHidden('type_asso_id');

		if ($this->pivot) {
			$this->pivot->makeHidden(['user_id', 'asso_id']);

			if ($this->pivot->semester_id === 0)
				$this->pivot->makeHidden('semester_id');
		}

		if ($this->sub) {
			foreach ($this->sub as $sub)
				$this->hideAssoData();
		}

		return $this;
	}

	public function contacts() {
		return $this->morphMany(Contact::class, 'owned_by');
	}

	public function isContactAccessibleBy(int $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isContactManageableBy(int $user_id): bool {
		return $this->hasOnePermission('contact', [
			'user_id' => $user_id,
		]);
	}

    public function calendars() {
    	return $this->morphMany(Calendar::class, 'owned_by');
    }

	public function isCalendarAccessibleBy(int $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isCalendarManageableBy(int $user_id): bool {
		return $this->hasOnePermission('calendar', [
			'user_id' => $user_id,
		]);
	}

    public function events() {
    	return $this->morphMany(Events::class, 'owned_by');
    }

	public function isEventAccessibleBy(int $user_id): bool {
		return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
	}

	public function isEventManageableBy(int $user_id): bool {
		return $this->hasOnePermission('event', [
			'user_id' => $user_id,
		]);
	}
}

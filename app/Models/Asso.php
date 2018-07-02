<?php

namespace App\Models;

use Cog\Contracts\Ownership\CanBeOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\HasMembers;
use \App\Traits\HasStages;

class Asso extends Model implements CanBeOwner
{
	use SoftDeletes, HasStages, HasMembers {
		HasMembers::members as membersAndFollowers;
		HasMembers::currentMembers as currentMembersAndFollowers;
		HasMembers::joiners as protected joinersFromHasMembers;
		HasMembers::currentJoiners as currentJoinersFromHasMembers;
		HasMembers::getUserRoles as getUsersRolesInThisAssociation;
	}

	protected $roleRelationTable = 'assos_members';

	protected $fillable = [
		'name', 'shortname', 'login', 'description', 'type_asso_id', 'parent_id',
	];

	public function type() {
		return $this->belongsTo(AssoType::class, 'type_asso_id');
	}

	public function contact() {
		return $this->morphMany(Contact::class, 'contactable');
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

	public function events() {
		return $this->belongsToMany(Event::class);
	}

	public function parent() {
	    return $this->hasOne(Asso::class, 'parent_id');
    }

	public function childs() {
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

				$roles = $roles->merge($role->allChilds());
				$role->makeHidden('childs');
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

	/**
	 * Permet de vérifier si l'utilisateur peut créer un contact pour ce model.
	 *
	 * @return bool
	 */
	public function canCreateContact() {
		return ($this->hasOneRole('resp communication', ['user_id' => \Auth::id()]) || \Auth::user()->hasOneRole('admin'));
	}

	/**
	 * Permet de vérifier si l'utilisateur peut modifier/supprimer un contact pour ce model.
	 *
	 * @return bool
	 */
	public function canModifyContact($contact) {
		if ($contact->contactable == $this) {
            return ($this->hasOneRole('resp communication', ['user_id' => \Auth::id()]) || \Auth::user()->hasOneRole('admin'));
        } else
        	return false;
	}
}

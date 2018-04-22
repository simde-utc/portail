<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Traits\HasMembers;

class Asso extends Model
{
	use HasMembers {
		HasMembers::members as membersAndFollowers;
		HasMembers::currentMembers as currentMembersAndFollowers;
		HasMembers::getUserRoles as getUsersRolesInThisAssociation;
	}

	protected $memberRelationTable = 'assos_roles';

	protected $fillable = [
		'name', 'login', 'description', 'type_asso_id', 'parent_id',
	];

	public function type() {
		return $this->belongsTo(AssoType::class);
	}

	public function contact() {
		return $this->hasMany(AssoContact::class, 'contacts_assos');
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

	public function members() {
		return $this->membersAndFollowers()->wherePivot('role_id', '!=', null);
	}

	public function currentMembers() {
		return $this->currentMembersAndFollowers()->wherePivot('role_id', '!=', null);
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

			foreach ($asso->getUserAssignedRoles($user_id, $semester_id) as $role) {
				$roles->push($role);

				foreach ($role->childs as $childRole)
					$roles->push($childRole);

				$role->makeHidden('childs');
			}

			$parent_id = $asso->parent_id;
		}

		return $roles;
	}
}

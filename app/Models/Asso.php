<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\HasMembers;

class Asso extends Model
{
	use SoftDeletes, HasMembers {
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
		return $this->morphMany(Asso::class, 'contacts');
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
	    return static::where('parent_id', $this->id);
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

	public static function getStage(int $stage, int $type_asso_id = null) {
		$assos = static::whereNull('parent_id')->with('type:id,name,description');

		if (!is_null($type_asso_id))
			$assos = $assos->where('type_asso_id', $type_asso_id);

		$assos = $assos->get();

		for ($i = 0; $i < $stage; $i++) {
			$before = $assos;
			$assos = collect();

			foreach ($before as $asso) {
				$childs = $asso->childs()->with('type:id,name,description');

				if (!is_null($childs))
					$childs = $childs->where('type_asso_id', $type_asso_id);

				$assos = $assos->merge($childs->get());
			}
		}

		return $assos;
	}

	public static function getFromStage(int $stage, int $type_asso_id = null) {
		$assos = static::whereNull('parent_id')->with('type:id,name,description');

		if (!is_null($type_asso_id))
			$assos = $assos->where('type_asso_id', $type_asso_id);

		$assos = $assos->get();
		$toAdd = $assos;

		for ($i = 0; $i < $stage; $i++) {
			$toAddChilds = $toAdd;
			$toAdd = collect();

			foreach ($toAddChilds as $asso) {
				$childs = $asso->childs()->with('type:id,name,description');

				if (!is_null($type_asso_id))
					$childs = $childs->where('type_asso_id', $type_asso_id);

				$asso->sub = $childs->get();
				$toAdd = $toAdd->merge($asso->sub);
			}
		}

		return $assos;
	}
}

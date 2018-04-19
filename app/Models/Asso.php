<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Traits\HasMembers;

class Asso extends Model
{
	use HasMembers {
		HasMembers::members as membersAndFollowers;
		HasMembers::currentMembers as currentMembersAndFollowers;
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Traits\HasRoles;

class Asso extends Model
{
	use HasRoles;

	protected $fillable = [
		'name', 'login', 'description', 'type_asso_id', 'parent_id',
	];

	public function members() {
		return $this->belongsToMany(User::class, 'assos_members')->whereNotNull('validated_by');
	}

	public function currentMembers() {
		return $this->belongsToMany(User::class, 'assos_members')->where('semester_id', Semester::getThisSemester()->id)->whereNotNull('validated_by');
	}

	public function joiners() {
		return $this->belongsToMany(User::class, 'assos_members')->whereNull('validated_by');
	}

	public function currentJoiners() {
		return $this->belongsToMany(User::class, 'assos_members')->where('semester_id', Semester::getThisSemester()->id)->whereNull('validated_by');
	}

	public function roles() {
		return $this->belongsToMany(Role::class, 'assos_members');
	}

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
		return $this->belongsToMany('App\Models\Article', 'assos_articles');
	}

	public function events() {
		return $this->belongsToMany('App\Models\Event');
	}

	public function parent() {
	    return $this->hasOne('App\Models\Asso');
    }
}

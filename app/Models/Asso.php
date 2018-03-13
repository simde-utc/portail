<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asso extends Model
{
	protected $fillable = [
		'name', 'login', 'description', 'type_asso_id', 'parent_id',
	];

	public function assoMember() {
		return $this->hasMany('App\Models\AssoMember');
	}

	public function members() {
		return $this->belongsToMany('App\Models\User', 'assos_members');
	}

	public function currentMembers() {
		return $this->belongsToMany('App\Models\User', 'assos_members')->where('semester_id', Semester::getThisSemester()->id);
	}

	public function type() {
		return $this->belongsTo('App\Models\AssoType');
	}

	public function contact() {
		return $this->morphMany('App\Models\Contact', 'contactable');
	}

	public function rooms() {
		// hasMany
	}

	public function reservations() {
		// hasMany
	}

	public function articles() {
		// belongsToMany
	}

	public function events() {
		// belongsToMany
	}

	public function parent() {
	    return $this->hasOne('App\Models\Asso');
    }
}

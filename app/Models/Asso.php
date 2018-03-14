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
		return $this->hasMany('App\Models\AssoContact', 'contacts_assos');
	}

	public function rooms() {
		return $this->hasMany('App\Models\Room');
	}

	public function reservations() {
		return $this->hasMany('App\Models\Reservations');
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

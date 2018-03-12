<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;

class User extends Authenticatable
{
		use HasRoles;

		protected $fillable = [
			'firstname', 'lastname', 'email', 'last_login_at',
		];

		protected $hidden = [
			'remember_token',
		];

		public function cas() {
			return $this->hasOne('App\Models\AuthCas');
		}
		public function password() {
			return $this->hasOne('App\Models\AuthPassword');
		}

		public function getCurrentAuth() {
			$services = config('auth.services');

			foreach ($services as $service => $serviceInfo) {
				if (method_exists($this, $service) && $this->$service()->exists())
					return $service;
			}

			return null;
		}

		public function assoMember() {
			return $this->hasMany('App\Models\AssoMember');
		}

		public function assos() {
			return $this->belongsToMany('App\Models\Asso', 'assos_members');
		}

		public function currentAssos() {
			return $this->belongsToMany('App\Models\Asso', 'assos_members')->where('semester_id', Semester::getThisSemester()->id);
		}

		public function groups() {
			return $this->belongsToMany('App\Models\Group', 'groups_members');
		}

		public function currentGroups() {
			return $this->belongsToMany('App\Models\Group', 'groups_members')->where('is_active', 1);
		}

		public function ownGroups() {
			return $this->hasMany('App\Models\Group');
		}

		public function contact() {
	        return $this->morphMany('App\Models\Contact', 'contactable');
	    }
}

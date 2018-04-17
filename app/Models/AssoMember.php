<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssoMember extends Model
{
    protected $table = 'assos_members';

    protected $fillable = [
        'asso_id', 'user_id', 'semester_id', 'role_id', 'validated_by'
    ];

	public function asso() {
		return $this->belongsTo('App\Models\Asso');
	}

	public function user() {
		return $this->belongsTo('App\Models\User');
	}

	public function semester() {
		return $this->belongsTo('App\Models\Semester');
	}

	public function role() {
		return $this->belongsTo('App\Models\Role');
	}

	public function permissions() {
		$permissions = $this->role()->permissions;

		$permission_ids = json_decode($this->permission_ids, true) ?? [];

		if ($permission_ids !== null && !empty($permission_ids)) {
			$permissions = array_merge(
				$permissions,
				Permission::whereIn('id', $permissions)->get()
			);
		}

		return $permissions;
	}
}

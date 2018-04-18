<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Semester;
use App\Models\User;

trait HasPermissions
{
    public static function bootHasPermissions() {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->permissions()->detach();
        });
    }

	protected function getPermissionRelationTable() {
		return $this->permissionRelationTable ?? $this->getTable().'_permissions';
	}

	public function permissions() {
		return $this->belongsToMany(Permission::class, $this->getPermissionRelationTable())->withPivot('semester_id', 'validated_by');
	}

	public function getUserAssignedPermissions($user_id = null, $semester_id = false, $needToBeValidated = true) {
		if (!($semester_id ?? false))
			$semester_id = Semester::getThisSemester()->id;

		$permissions = $this->permissions();

		if ($permissions === null)
		return new Collection;

		if ($this->getTable() !== 'users')
			$permissions = $permissions->wherePivot('user_id', $user_id);

		$relatedTable = $this->getPermissionRelationTable();

		$permissions = $permissions->where(function ($query) use ($semester_id, $relatedTable) {
			$query->where($relatedTable.'.semester_id', $semester_id)->orWhere($relatedTable.'.semester_id', '=', null);
		});

		if ($needToBeValidated)
			$permissions = $permissions->wherePivot('validated_by', '!=', null);

		return $permissions->get();
	}

	public function getUserPermissions($user_id = null, $semester_id = false) {
		return $this->getUserAssignedPermissions($user_id, $semester_id);
	}
}

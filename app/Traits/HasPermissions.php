<?php

namespace App\Traits;

use Illuminate\Support\Collection;
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
		return $this->belongsToMany(Permission::class, $this->getPermissionRelationTable())->withPivot('semester_id', 'validated_by', 'created_at', 'updated_at');
	}

	public function assignPermission($permissions, array $data = [], bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		$addPermissions = [];

		if ($data['validated_by'] ?? false)
			$manageablePermissions = $this->getUserPermissions($data['validated_by']);

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users') as $permission) {
			if ($permission === null)
				throw new \Exception('Il n\'est pas autorisé d\'associer cette permission');

			if (!$force) {
				$relatedTable = $this->getPermissionRelationTable();
				$semester_id = $data['semester_id'];

				if ($permission->limited_at !== null) {
					$users = $permission->users()->where(function ($query) use ($semester_id, $relatedTable) {
						$query->where($relatedTable.'.semester_id', $semester_id)->orWhere($relatedTable.'.semester_id', '=', null);
					})->wherePivot('validated_by', '!=', null);

					if ($users->count() >= $permission->limited_at)
						throw new \Exception('Le nombre de personnes ayant cette permission a été dépassé. Limité à '.$permission->limited_at);
				}

				if ($data['validated_by'] ?? false) {
					if (!$manageablePermissions->contains('id', $permission->id) && !$manageablePermissions->contains('type', 'admin'))
						throw new \Exception('La personne demandant la validation n\'est pas habilitée à donner cette permission: '.$permission->name);
				}
			}

			$addPermissions[$permission->id] = $data;
		}

		$this->permissions()->withTimestamps()->attach($addPermissions);

		return $this;
	}

    public function updatePermission($permissions, array $data = [], array $updatedData = [], bool $force = false) {
		if (!isset($updatedData['semester_id']))
			$updatedData['semester_id'] = Semester::getThisSemester()->id;

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($updatedData['validated_by'] ?? false)
			$manageablePermissions = $this->getUserPermissions($updatedData['validated_by']);

		$updatedPermissions = [];

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users') as $permission) {
			if ($permission === null)
				throw new \Exception('Le permission '.$permission.' n\'existe pas ou ne correspond à ce type de modèle');

			if (!$force && ($updatedData['validated_by'] ?? false)) {
				if (!$manageablePermissions->contains('id', $permission->id) && (!$manageablePermissions->contains('type', 'admin')))
					throw new \Exception('La personne demandant la validation n\'est pas habilitée à modifier cette permission: '.$permission->name);
			}

			array_push($updatedPermissions, $permission->id);
		}

		$toUpdate = $this->permissions()->withTimestamps();

		foreach ($data as $key => $value)
			$toUpdate->wherePivot($key, $value);

		foreach ($updatedPermissions as $updatedPermission)
			$toUpdate->updateExistingPivot($updatedPermission, $updatedData);

		return $this;
    }

    public function removePermission($permissions, array $data = [], bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($data['validated_by'] ?? false)
			$manageablePermissions = $this->getUserPermissions($data['validated_by']);

		$delPermissions = [];

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users') as $permission) {
			if ($permission === null)
				throw new \Exception('Le permission '.$permission.' n\'existe pas ou ne correspond à ce type de modèle');

			if (!$force && ($data['validated_by'] ?? false)) {
				if (!$manageablePermissions->contains('id', $permission->id) && (!$manageablePermissions->contains('type', 'admin')))
					throw new \Exception('La personne demandant la suppression n\'est pas habilitée à retirer cette permission: '.$permission->name);
			}

			array_push($delPermissions, $permission->id);
		}

		$toDetach = $this->permissions();

		if ($data['validated_by'] ?? false)
			unset($data['validated_by']);

		foreach ($data as $key => $value)
			$toDetach->wherePivot($key, $value);

		$toDetach->detach($delPermissions);

		return $this;
    }

    public function syncPermissions($permissions, array $data = [], bool $force = false) {
		$currentPermissions = $this->getUserAssignedPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null, false)->pluck('id');
		$permissions = Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users')->pluck('id');
		$intersectedPermissions = $currentPermissions->intersect($permissions);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['user_id'] ?? false)
			$oldData['user_id'] = $data['user_id'];

        return $this->assignPermission($permissions->diff($currentPermissions), $data, $force)->updatePermission($intersectedPermissions, $oldData, $data, $force)->removePermission($currentPermissions->diff($permissions), $data, $force);
    }

    public function hasOnePermission($permission, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Permission::getPermissions(stringToArray($permission), $this->getTable() === 'users')->pluck('id')->intersect($this->getUserPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null)->pluck('id'))->isNotEmpty();
    }

    public function hasAllPermissions($permission, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Permission::getPermissions(stringToArray($permission), $this->getTable() === 'users')->pluck('id')->diff($this->getUserPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'])->pluck('id'))->isEmpty();
    }

	public function getUserAssignedPermissions($user_id = null, $semester_id = false, $needToBeValidated = true) {
		if (!($semester_id ?? false))
			$semester_id = Semester::getThisSemester()->id;

		$permissions = $this->permissions();

		if ($permissions === null)
		return new Collection;

		if ($this->getTable() !== 'users' || $user_id !== null)
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

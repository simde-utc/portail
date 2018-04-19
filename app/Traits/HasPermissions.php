<?php

namespace App\Traits;

use AppRoleExceptions\PortailException;
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

	public function assignPermissions($permissions, array $data = [], bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		$addPermissions = [];

		if (isset($data['validated_by']))
			$manageablePermissions = $this->getUserPermissions($data['validated_by']);

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users') as $permission) {
			if (!$force) {
				$relatedTable = $this->getPermissionRelationTable();
				$semester_id = $data['semester_id'];

				if ($permission->limited_at !== null) {
					$users = $permission->users()->where(function ($query) use ($semester_id, $relatedTable) {
						$query->where($relatedTable.'.semester_id', $semester_id)->orWhere($relatedTable.'.semester_id', '=', null);
					})->wherePivot('validated_by', '!=', null);

					if ($users->count() >= $permission->limited_at)
						throw new PortailException('Le nombre de personnes ayant cette permission a été dépassé. Limité à '.$permission->limited_at);
				}

				if (isset($data['validated_by'])) {
					if (!$manageablePermissions->contains('id', $permission->id) && !$manageablePermissions->contains('type', 'admin'))
						throw new PortailException('La personne demandant la validation n\'est pas habilitée à donner cette permission: '.$permission->name);
				}
			}

			$addPermissions[$permission->id] = $data;
		}

		try {
			$this->permissions()->withTimestamps()->attach($addPermissions);
		} catch (\Exception $e) {
			throw new RoleException('Une des personnes possède déjà trop de permissions');
		}

		return $this;
	}

    public function updatePermissions($permissions, array $data = [], array $updatedData = [], bool $force = false) {
		if (!isset($updatedData['semester_id']))
			$updatedData['semester_id'] = Semester::getThisSemester()->id;

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if (isset($updatedData['validated_by']))
			$manageablePermissions = $this->getUserPermissions($updatedData['validated_by']);

		$updatedPermissions = [];

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users') as $permission) {
			if (!$force && isset($updatedData['validated_by'])) {
				if (!$manageablePermissions->contains('id', $permission->id) && (!$manageablePermissions->contains('type', 'admin')))
					throw new PortailException('La personne demandant la validation n\'est pas habilitée à modifier cette permission: '.$permission->name);
			}

			array_push($updatedPermissions, $permission->id);
		}

		$toUpdate = $this->permissions()->withTimestamps();

		foreach ($data as $key => $value)
			$toUpdate->wherePivot($key, $value);

		try {
			foreach ($updatedPermissions as $updatedPermission)
				$toUpdate->updateExistingPivot($updatedPermission, $updatedData);
		} catch (\Exception $e) {
			throw new MemberException('Les données d\'une permission ne peuvent être modifiées');
		}

		return $this;
    }

    public function removePermissions($permissions, array $data = [], int $removed_by = null, bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($removed_by !== null)
			$manageablePermissions = $this->getUserPermissions($removed_by);

		$delPermissions = [];

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users') as $permission) {
			if (!$force && $removed_by !== null) {
				if (!$manageablePermissions->contains('id', $permission->id) && (!$manageablePermissions->contains('type', 'admin')))
					throw new PortailException('La personne demandant la suppression n\'est pas habilitée à retirer cette permission: '.$permission->name);
			}

			array_push($delPermissions, $permission->id);
		}

		$toDetach = $this->permissions();

		foreach ($data as $key => $value)
			$toDetach->wherePivot($key, $value);

		try {
			$toDetach->detach($delPermissions);
		} catch (\Exception $e) {
			throw new MemberException('Une erreur a été recontrée à la suppression d\'une permission');
		}

		return $this;
    }

    public function syncPermissions($permissions, array $data = [], int $removed_by = null, bool $force = false) {
		$currentPermissions = $this->getUserAssignedPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null, false)->pluck('id');
		$permissions = Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users')->pluck('id');
		$intersectedPermissions = $currentPermissions->intersect($permissions);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['user_id'] ?? false)
			$oldData['user_id'] = $data['user_id'];

        return $this->assignPermissions($permissions->diff($currentPermissions), $data, $force)->updatePermissions($intersectedPermissions, $oldData, $data, $force)->removePermissions($currentPermissions->diff($permissions), $data, $removed_by, $force);
    }

    public function hasOnePermission($permissions, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users')->pluck('id')->intersect($this->getUserPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null)->pluck('id'))->isNotEmpty();
    }

    public function hasAllPermissions($permissions, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Permission::getPermissions(stringToArray($permissions), $this->getTable() === 'users')->pluck('id')->diff($this->getUserPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'])->pluck('id'))->isEmpty();
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

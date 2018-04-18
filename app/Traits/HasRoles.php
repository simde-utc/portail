<?php

namespace App\Traits;

use App\Traits\HasPermissions;

use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Permission;
use App\Models\User;

trait HasRoles
{
	use HasPermissions {
		getUserPermissions as getUserPermissionsFromPermissions;
	}

    public static function bootHasRoles() {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->roles()->detach();
        });
    }

	protected function getRoleRelationTable() {
		return $this->roleRelationTable ?? $this->getTable().'_roles';
	}

	public function roles() {
		return $this->belongsToMany(Role::class, $this->getRoleRelationTable())->withPivot('semester_id', 'validated_by');
	}

	public function assignRole($roles, array $data = [], bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		$addRoles = [];

		if ($data['validated_by'] ?? false)
			$manageableRoles = $this->getUserRoles($data['validated_by']);

		foreach (Role::getRoles(stringToArray($roles), $this->getTable()) as $role) {
			if ($role === null)
				throw new \Exception('Il n\'est pas autorisé d\'associer ce role');

			if (!$force) {
				$relatedTable = $this->getRoleRelationTable();
				$semester_id = $data['semester_id'];

				if ($role->limited_at !== null) {
					$users = $role->users()->where(function ($query) use ($semester_id, $relatedTable) {
						$query->where($relatedTable.'.semester_id', $semester_id)->orWhere($relatedTable.'.semester_id', '=', null);
					})->wherePivot('validated_by', '!=', null);

					if ($users->count() >= $role->limited_at)
						throw new \Exception('Le nombre de personnes ayant ce role a été dépassé');
				}

				if ($data['validated_by'] ?? false) {
					if (!$manageableRoles->contains('id', $role->id) && !$manageableRoles->contains('type', 'admin'))
						throw new \Exception('La personne validatrice n\'est pas habilitée à donner ce rôle: '.$role->name);
				}
			}

			$addRoles[$role->id] = $data;
		}

		$this->roles()->withTimestamps()->attach($addRoles);

		return $this;
	}

    public function updateRole($roles, array $data = [], array $updatedData = [], bool $force = false) {
		if (!isset($updatedData['semester_id']))
			$updatedData['semester_id'] = Semester::getThisSemester()->id;

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($updatedData['validated_by'] ?? false)
			$manageableRoles = $this->getUserRoles($updatedData['validated_by']);

		$updatedRoles = [];

		foreach (Role::getRoles(stringToArray($roles), $this->getTable()) as $role) {
			if ($role === null)
				throw new \Exception('Le role '.$role.' n\'existe pas ou ne correspond à ce type de modèle');

			if (!$force && $updatedData['validated_by'] ?? false) {
				if (!$manageableRoles->contains('id', $role->id) && (!$manageableRoles->contains('type', 'admin') || $role->childRoles->contains('type', 'admin')))
					throw new \Exception('La personne demandant la suppression n\'est pas habilitée à retirer ce rôle: '.$role->name);
			}

			array_push($updatedRoles, $role->id);
		}

		$toUpdate = $this->roles()->withTimestamps();

		if ($data['validated_by'] ?? false)
			unset($data['validated_by']);

		foreach ($data as $key => $value)
			$toUpdate->wherePivot($key, $value);

		foreach ($updatedRoles as $updatedRole)
			$toUpdate->updateExistingPivot($updatedRole, $updatedData);

		return $this;
    }

    public function removeRole($roles, array $data = [], bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($data['validated_by'] ?? false)
			$manageableRoles = $this->getUserRoles($data['validated_by']);

		$delRoles = [];

		foreach (Role::getRoles(stringToArray($roles), $this->getTable()) as $role) {
			if ($role === null)
				throw new \Exception('Le role '.$role.' n\'existe pas ou ne correspond à ce type de modèle');

			if (!$force && $data['validated_by'] ?? false) {
				if (!$manageableRoles->contains('id', $role->id) && (!$manageableRoles->contains('type', 'admin') || $role->childRoles->contains('type', 'admin')))
					throw new \Exception('La personne demandant la suppression n\'est pas habilitée à retirer ce rôle: '.$role->name);
			}

			array_push($delRoles, $role->id);
		}

		$toDetach = $this->roles();

		if ($data['validated_by'] ?? false)
			unset($data['validated_by']);

		foreach ($data as $key => $value)
			$toDetach->wherePivot($key, $value);

		$toDetach->detach($delRoles);

		return $this;
    }

    public function syncRoles($roles, array $data = [], bool $force = false) {
		$currentRoles = $this->getUserAssignedRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null, false)->pluck('id');
		$roles = Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id');
		$intersectedRoles = $currentRoles->intersect($roles);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['user_id'] ?? false)
			$oldData['user_id'] = $data['user_id'];

        return $this->assignRole($roles->diff($currentRoles), $data, $force)->updateRole($intersectedRoles, $oldData, $data, $force)->removeRole($currentRoles->diff($roles), $data, $force);
    }

    public function hasOneRole($roles, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id')->intersect($this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null)->pluck('id'))->isNotEmpty();
    }

    public function hasAllRoles($roles, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id')->diff($this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'])->pluck('id'))->isEmpty();
    }

	public function getUserAssignedRoles($user_id = null, $semester_id = false, $needToBeValidated = true) {
		if (!($semester_id ?? false))
			$semester_id = Semester::getThisSemester()->id;

		$roles = $this->roles();

		if ($roles === null)
			return new Collection;

		if ($this->getTable() !== 'users')
			$roles = $roles->wherePivot('user_id', $user_id);

		$relatedTable = $this->getRoleRelationTable();

		$roles = $roles->where(function ($query) use ($semester_id, $relatedTable) {
			$query->where($relatedTable.'.semester_id', $semester_id)->orWhere($relatedTable.'.semester_id', '=', null);
		});

		if ($needToBeValidated)
			$roles = $roles->wherePivot('validated_by', '!=', null);

		return $roles->get();
	}

	public function getUserRoles($user_id = null, $semester_id = false) {
		if (!($semester_id ?? false))
			$semester_id = Semester::getThisSemester()->id;

		$roles = $this->getUserAssignedRoles($user_id, $semester_id);

		foreach ($roles as $role) {
			foreach ($role->childRoles as $chilRole)
				$roles->push($chilRole);
		}

		if ($this->getTable() !== 'users') {
			foreach ((new User)->getUserRoles($user_id, $semester_id) as $userRole)
				$roles->push($userRole);
		}

		return $roles;
	}

	public function getUserPermissions($user_id = null, $semester_id = false) {
		$permissions = $this->getUserPermissionsFromPermissions($user_id, $semester_id);

		dd($permissions);

		foreach ($this->getUserRoles($user_id, $semester_id)->pluck('id') as $role_id)
			$permissions = $permissions->merge(Role::find($role_id)->permissions);

		return $permissions;
	}
}

<?php

namespace App\Traits;

use AppPortailExceptions\PortailException;
use App\Traits\HasPermissions;
use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Permission;
use App\Models\User;

trait HasRoles
{
	use HasPermissions {
		HasPermissions::getUserPermissions as getUserPermissionsFromHasPermissions;
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
	    return $this->belongsToMany(Role::class, $this->getRoleRelationTable());
	}

	public function assignRoles($roles, array $data = [], bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		$addRoles = [];

		if (isset($data['validated_by']))
			$manageableRoles = $this->getUserRoles($data['validated_by']);

		foreach (Role::getRoles(stringToArray($roles), $this->getTable()) as $role) {
			if (!$force) {
				$relatedTable = $this->getRoleRelationTable();
				$semester_id = $data['semester_id'];

				if ($role->limited_at !== null) {
					$users = $role->users()->where(function ($query) use ($semester_id, $relatedTable) {
						$query->where($relatedTable.'.semester_id', $semester_id)->orWhere($relatedTable.'.semester_id', '=', null);
					})->wherePivot('validated_by', '!=', null);

					if ($users->count() >= $role->limited_at)
						throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. Limité à '.$role->limited_at);
				}

				if (isset($data['validated_by'])) {
					if (!$manageableRoles->contains('id', $role->id) && !$manageableRoles->contains('type', 'admin'))
						throw new PortailException('La personne demandant la validation n\'est pas habilitée à donner ce rôle: '.$role->name);
				}
			}

			$addRoles[$role->id] = $data;
		}

		try {
			$this->roles()->withTimestamps()->attach($addRoles);
		} catch (\Exception $e) {
			throw new PortailException('Une des personnes possède déjà un rôle');
		}

		return $this;
	}

    public function updateRoles($roles, array $data = [], array $updatedData = [], bool $force = false) {
		if (!isset($updatedData['semester_id']))
			$updatedData['semester_id'] = Semester::getThisSemester()->id;

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if (isset($updatedData['validated_by']))
			$manageableRoles = $this->getUserRoles($updatedData['validated_by']);

		$updatedRoles = [];

		foreach (Role::getRoles(stringToArray($roles), $this->getTable()) as $role) {
			if (!$force && isset($updatedData['validated_by'])) {
				if (!$manageableRoles->contains('id', $role->id) && (!$manageableRoles->contains('type', 'admin') || $role->childRoles->contains('type', 'admin')))
					throw new PortailException('La personne demandant la validation n\'est pas habilitée à modifier ce rôle: '.$role->name);
			}

			array_push($updatedRoles, $role->id);
		}

		$toUpdate = $this->roles()->withTimestamps();

		foreach ($data as $key => $value)
			$toUpdate->wherePivot($key, $value);

		try {
			foreach ($updatedRoles as $updatedRole)
				$toUpdate->updateExistingPivot($updatedRole, $updatedData);
		} catch (\Exception $e) {
			throw new MemberException('Les données d\'un role ne peuvent être modifiées');
		}

		return $this;
    }

    public function removeRoles($roles, array $data = [], int $removed_by = null, bool $force = false) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($removed_by !== null)
			$manageableRoles = $this->getUserRoles($removed_by);

		$delRoles = [];

		foreach (Role::getRoles(stringToArray($roles), $this->getTable()) as $role) {
			if (!$force && $removed_by !== null) {
				if (!$manageableRoles->contains('id', $role->id) && (!$manageableRoles->contains('type', 'admin') || $role->childRoles->contains('type', 'admin')))
					throw new PortailException('La personne demandant la suppression n\'est pas habilitée à retirer ce rôle: '.$role->name);
			}

			array_push($delRoles, $role->id);
		}

		$toDetach = $this->roles();

		foreach ($data as $key => $value)
			$toDetach->wherePivot($key, $value);

		try {
			$toDetach->detach($delRoles);
		} catch (\Exception $e) {
			throw new MemberException('Une erreur a été recontrée à la suppression d\'un role utilisateur');
		}

		return $this;
    }

    public function syncRoles($roles, array $data = [], int $removed_by = null, bool $force = false) {
		$currentRoles = $this->getUserAssignedRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null, false)->pluck('id');
		$roles = Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id');
		$intersectedRoles = $currentRoles->intersect($roles);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['user_id'] ?? false)
			$oldData['user_id'] = $data['user_id'];

        return $this->assignRoles($roles->diff($currentRoles), $data, $force)->updateRoles($intersectedRoles, $oldData, $data, $force)->removeRoles($currentRoles->diff($roles), $data, $removed_by, $force);
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

		if ($this->getTable() !== 'users' || $user_id !== null)
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
			foreach (User::find($user_id)->getUserRoles(null, $semester_id) as $userRole)
				$roles->push($userRole);
		}

		return $roles;
	}

	public function getUserPermissions($user_id = null, $semester_id = false) {
		$permissions = $this->getUserPermissionsFromHasPermissions($user_id, $semester_id);

		foreach ($this->getUserRoles($user_id, $semester_id)->pluck('id') as $role_id)
			$permissions = $permissions->merge(Role::find($role_id)->permissions);

		return $permissions;
	}
}

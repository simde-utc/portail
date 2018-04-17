<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Permission;
use App\Models\User;
use Carbon\Carbon;

trait HasRoles
{
    use HasPermissions;

    public static function bootHasRoles()
	{
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->roles()->detach();
        });
    }

	public function assignRole($roles, array $data = [])
	{
		if (($data['semester_id'] ?? null) === null)
			$data['semester_id'] = Semester::getThisSemester()->id;

		$addRoles = [];

		if ($data['validated_by'] ?? false)
			$manageableRoles = $this->getRolesManageableBy($data['validated_by']);

		foreach (stringToArray($roles) as $role) {
			$one = Role::getRole($role, $this->getTable());

			if ($one === null)
				throw new \Exception('Il n\'est pas autorisé d\'associer ce role');

			if ($one->limited_at !== null && $one->users()->wherePivot('semester_id', $data['semester_id'])->wherePivot('validated_by', '!=', null)->count() >= $one->limited_at)
				throw new \Exception('Le nombre de personnes ayant ce role a été dépassé');

			if ($data['validated_by'] ?? false) {
				if (!$manageableRoles->contains('id', $one->id) && !$manageableRoles->contains('type', 'admin'))
					throw new \Exception('La personne validatrice n\'est pas habilitée à donner ce rôle: '.$one->name);
			}

			$addRoles[$one->id] = $data;
		}

		$this->roles()->withTimestamps()->attach($addRoles);

		return $this;
	}

    public function updateRole($roles, array $data = [], array $updatedData = [])
	{
		if (($updatedData['semester_id'] ?? null) === null)
			$updatedData['semester_id'] = Semester::getThisSemester()->id;

			if (($data['semester_id'] ?? null) === null)
				$data['semester_id'] = Semester::getThisSemester()->id;

		if ($updatedData['validated_by'] ?? false)
			$manageableRoles = $this->getRolesManageableBy($updatedData['validated_by']);

		$updatedRoles = [];

		foreach (stringToArray($roles) as $role) {
			$one = Role::getRole($role, $this->getTable());

			if ($one === null)
				throw new \Exception('Le role '.$role.' n\'existe pas ou ne correspond à ce type de modèle');

			if ($updatedData['validated_by'] ?? false) {
				if (!$manageableRoles->contains('id', $one->id) && (!$manageableRoles->contains('type', 'admin') || $one->childRoles->contains('type', 'admin')))
					throw new \Exception('La personne demandant la suppression n\'est pas habilitée à retirer ce rôle: '.$one->name);
			}

			array_push($updatedRoles, $one->id);
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

    public function removeRole($roles, array $data = [])
	{
		if (($data['semester_id'] ?? null) === null)
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($data['validated_by'] ?? false)
			$manageableRoles = $this->getRolesManageableBy($data['validated_by']);

		$delRoles = [];

		foreach (stringToArray($roles) as $role) {
			$one = Role::getRole($role, $this->getTable());

			if ($one === null)
				throw new \Exception('Le role '.$role.' n\'existe pas ou ne correspond à ce type de modèle');

			if ($data['validated_by'] ?? false) {
				if (!$manageableRoles->contains('id', $one->id) && (!$manageableRoles->contains('type', 'admin') || $one->childRoles->contains('type', 'admin')))
					throw new \Exception('La personne demandant la suppression n\'est pas habilitée à retirer ce rôle: '.$one->name);
			}

			array_push($delRoles, $one->id);
		}

		$toDetach = $this->roles();

		if ($data['validated_by'] ?? false)
			unset($data['validated_by']);

		foreach ($data as $key => $value)
			$toDetach->wherePivot($key, $value);

		$toDetach->detach($delRoles);

		return $this;
    }

    public function syncRoles($roles, array $data = [])
	{
		$currentRoles = $this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null, false)->pluck('id');
		$roles = Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id');
		$intersectedRoles = $currentRoles->intersect($roles);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['user_id'] ?? false)
			$oldData['user_id'] = $data['user_id'];

        return $this->assignRole($roles->diff($currentRoles), $data)->updateRole($intersectedRoles, $oldData, $data)->removeRole($currentRoles->diff($roles), $data);
    }

    public function hasAnyRole($roles, array $data = []): bool
    {
		if (($data['semester_id'] ?? null) === null)
			$data['semester_id'] = Semester::getThisSemester()->id;

        return Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id')->intersect($this->roles->pluck('id'))->isNotEmpty();
    }

    public function hasAllRoles($roles, array $data = []): bool
    {
		if (($data['semester_id'] ?? null) === null)
			$data['semester_id'] = Semester::getThisSemester()->id;

		$roles = Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id');

        return $roles->intersect($this->roles->pluck('id')) == $roles;
    }

	public function getUserRoles($user_id, $semester_id = null, $needToBeValidated = true)
	{
		if (($semester_id ?? null) === null)
			$semester_id = Semester::getThisSemester()->id;
		// TODO à rendre dynamique

		if ($this->getTable() === 'users')
			$roles = static::find($user_id)->roles();
		else
			$roles = $this->roles()->wherePivot('user_id', $user_id);

		if ($roles === null)
			return new Collection;
		else {
			$roles = $roles->wherePivot('semester_id', $semester_id);

			if ($needToBeValidated)
				$roles = $roles->wherePivot('validated_by', '!=', null);

			return $roles->get();
		}

	}

	public function getRolesManageableBy($user_id, $semester_id = null)
	{
		if (($semester_id ?? null) === null)
			$semester_id = Semester::getThisSemester()->id;

		$roles = $this->getUserRoles($user_id, $semester_id);

		foreach ($roles as $role) {
			foreach ($role->childRoles as $chilRole)
				$roles->push($chilRole);
		}

		if ($this->getTable() !== 'users') {
			foreach ((new User)->getRolesManageableBy($user_id, $semester_id) as $userRole)
				$roles->push($userRole);
		}

		return $roles;
	}

    /*
    public function getDirectPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getRoleTypes(): Collection
    {
        return $this->roles->pluck('type');
    }*/
}

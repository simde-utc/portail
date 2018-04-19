<?php

namespace App\Traits;

use App\Exceptions\PortailException;
use App\Traits\HasRoles;
use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Semester;
use App\Models\Permission;
use App\Models\Visibility;
use App\Models\User;

trait HasMembers
{
	use HasRoles;

    public static function bootHasMembers() {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->members()->detach();
        });
    }

	protected function getMemberRelationTable() {
		return $this->memberRelationTable ?? $this->getTable().'_members';
	}

	public function members() {
		return $this->belongsToMany(User::class, $this->getMemberRelationTable())->whereNotNull('validated_by')->withPivot('semester_id', 'validated_by', 'created_at', 'updated_at');
	}

	public function currentMembers() {
		$relatedTable = $this->getMemberRelationTable();

		return $this->belongsToMany(User::class, $relatedTable)->where(function ($query) use ($relatedTable) {
			$query->where($relatedTable.'.semester_id', Semester::getThisSemester()->id)->orWhere($relatedTable.'.semester_id', '=', null);
		})->whereNotNull('validated_by')->withPivot('semester_id', 'validated_by', 'created_at', 'updated_at');
	}

	public function joiners() {
		return $this->belongsToMany(User::class, $this->getMemberRelationTable())->whereNull('validated_by')->withPivot('semester_id', 'validated_by', 'created_at', 'updated_at');
	}

	public function currentJoiners() {
		$relatedTable = $this->getMemberRelationTable();

		return $this->belongsToMany(User::class, $relatedTable)->where(function ($query) use ($relatedTable) {
			$query->where($relatedTable.'.semester_id', Semester::getThisSemester()->id)->orWhere($relatedTable.'.semester_id', '=', null);
		})->whereNotNull('validated_by')->withPivot('semester_id', 'validated_by', 'created_at', 'updated_at');
	}

	public function assignMembers($members, array $data = [], bool $force = false) {
		if (!$force && $this->visibility_id != null && $this->visibility_id < Visibility::findByType('private')->id) {
			if (isset($data['validated_by'])) {
				$manageablePermissions = $this->getUserPermissions($data['validated_by']);

				if (!$manageablePermissions->contains('type', 'members') && !$manageablePermissions->contains('type', 'admin'))
					throw new PortailException('La personne demandant la validation n\'est pas habilitée à ajouter de membres, il s\'agit d\'un groupe fermé');
			}
			else
				throw new PortailException('L\'ajout de membre est fermé. Il est nécessaire qu\'une personne ayant les droits d\'ajout ajoute la personne');
		}

		$members = User::getUsers(stringToArray($members));

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if (!$force && isset($data['validated_by'])) {
			if ($data['role_id'] ?? false) {
				$manageableRoles = $this->getUserRoles($data['validated_by']);
				$role = Role::find($data['role_id'], $this->getTable());

				if (!$manageableRoles->contains('id', $data['role_id']) && !$manageableRoles->contains('type', 'admin'))
					throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à donner ce rôle: '.$role->name);

				if ($this->roles()->wherePivot('role_id', $data['role_id'])->wherePivotIn('user_id', $members->pluck('id'), false)->get()->count() > $role->limited_at - $members->count())
					throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. Limité à '.$role->limited_at);
			}

			$manageablePermissions = $this->getUserPermissions($data['validated_by']);

			if (!$manageablePermissions->contains('type', 'members') && !$manageablePermissions->contains('type', 'admin'))
				throw new PortailException('La personne demandant la validation n\'est pas habilitée à ajouter des membres');
		}

		$addMembers = [];

		foreach ($members as $member)
			$addMembers[$member->id] = $data;

		try {
			$this->members()->withTimestamps()->attach($addMembers);
		} catch (\Exception $e) {
			throw new PortailException('Une des personnes est déjà membre');
		}

		return $this;
	}

    public function updateMembers($members, array $data = [], array $updatedData = [], bool $force = false) {
		$members = User::getUsers(stringToArray($members));

		if (!isset($updatedData['semester_id']))
			$updatedData['semester_id'] = Semester::getThisSemester()->id;

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if (!$force && isset($data['validated_by'])) {
			$manageableRoles = $this->getUserRoles($data['validated_by']);
			$manageablePermissions = $this->getUserPermissions($data['validated_by']);

			if ($data['role_id'] ?? false) {
				$role = Role::find($data['role_id'], $this->getTable());

				if (!$manageableRoles->contains('id', $data['role_id']) && !$manageableRoles->contains('type', 'admin'))
					throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à modifier ce rôle: '.$role->name);
			}

			if ($updatedData['role_id'] ?? false) {
				$role = Role::find($updatedData['role_id'], $this->getTable());

				if (!$manageableRoles->contains('id', $updatedData['role_id']) && !$manageableRoles->contains('type', 'admin'))
					throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à modifier ce rôle: '.$role->name);

				if ($this->roles()->wherePivot('role_id', $updatedData['role_id'])->wherePivotIn('user_id', $members->pluck('id'), false)->get()->count() > $role->limited_at - $members->count())
					throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. Limité à '.$role->limited_at);
			}

			if (!$manageablePermissions->contains('type', 'members') && !$manageablePermissions->contains('type', 'admin'))
				throw new PortailException('La personne demandant la validation n\'est pas habilitée à modifier les membres');
		}

		$updatedMembers = [];

		foreach ($members as $member)
			array_push($updatedMembers, $member->id);

		$toUpdate = $this->members()->withTimestamps();

		foreach ($data as $key => $value)
			$toUpdate->wherePivot($key, $value);

		try {
			foreach ($updatedMembers as $updatedMember)
				$toUpdate->updateExistingPivot($updatedMember, $updatedData);
		} catch (\Exception $e) {
			throw new PortailException('Les données d\'un membre ne peuvent être modifiées');
		}

		return $this;
    }

    public function removeMembers($members, array $data = [], int $removed_by = null, bool $force = false) {
		$members = User::getUsers(stringToArray($members));

		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

		if ($removed_by !== null)
			$manageableMembers = $this->getUserMembers($removed_by);

		if (!$force && $removed_by !== null) {
			$manageableRoles = $this->getUserRoles($removed_by);
			$manageablePermissions = $this->getUserPermissions($removed_by);

			if ($data['role_id'] ?? false) {
				$role = Role::find($data['role_id'], $this->getTable());

				if (!$manageableRoles->contains('id', $data['role_id']) && !$manageableRoles->contains('type', 'admin'))
					throw new PortailException('La personne demandant l\'affectation de rôle n\'est pas habilitée à modifier ce rôle: '.$role->name);
			}

			if (!$manageablePermissions->contains('type', 'members') && !$manageablePermissions->contains('type', 'admin'))
				throw new PortailException('La personne demandant la validation n\'est pas habilitée à donner ce rôle: '.$member->name);
		}

		$delMembers = [];

		foreach ($members as $member)
			array_push($delMembers, $member->id);

		$toDetach = $this->members();

		foreach ($data as $key => $value)
			$toDetach->wherePivot($key, $value);

		try {
			$toDetach->detach($delMembers);
		} catch (\Exception $e) {
			throw new PortailException('Une erreur a été recontrée à la suppression d\'un membre');
		}

		return $this;
    }

    public function syncMembers($members, array $data = [], int $removed_by = null, bool $force = false) {
		$currentMembers = $this->getUserAssignedMembers($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null, false)->pluck('id');
		$members = User::getUsers(stringToArray($members))->pluck('id');
		$intersectedMembers = $currentMembers->intersect($members);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['role_id'] ?? false)
			$oldData['role_id'] = $data['role_id'];

        return $this->assignMembers($members->diff($currentMembers), $data, $force)->updateMembers($intersectedMembers, $oldData, $data, $force)->removeMembers($currentMembers->diff($members), $data, $removed_by, $force);
    }

    public function hasOneMember($members, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return User::getUsers(stringToArray($members))->pluck('id')->intersect($this->getUserMembers($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null)->pluck('id'))->isNotEmpty();
    }

    public function hasAllMembers($members, array $data = []) {
		if (!isset($data['semester_id']))
			$data['semester_id'] = Semester::getThisSemester()->id;

        return User::getUsers(stringToArray($members))->pluck('id')->diff($this->getUserMembers($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'])->pluck('id'))->isEmpty();
    }
}

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
		// On rename les méthodes qu'on veut réutiliser et redéfinir
		HasPermissions::getUserPermissions as getUserPermissionsFromHasPermissions;
	}

	/**
	 * Méthode appelée au chargement du trait
	 */
    public static function bootHasRoles() {
        static::deleting(function ($model) {
			// Si on souhaite supprimer la ressources, on supprime les membres associés
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->roles()->detach();
        });
    }

	/**
	 * Récupération du nom de la table de relation
	 * @return string
	 */
	protected function getRoleRelationTable() {
		return $this->roleRelationTable ?? $this->getTable().'_roles';
	}

	/**
	 * Liste des roles attribués
	 */
	public function roles() {
	    return $this->belongsToMany(Role::class, $this->getRoleRelationTable())->withPivot('semester_id', 'validated_by', 'created_at', 'updated_at');
	}

	/**
	 * Permet d'assigner un ou plusieurs roles attribués en fonction des données fournis
	 * @param  string/array/Collection  $roles
	 * @param  array   $data    Possibilité d'affecter role_id, semester_id, validated_by, user_id
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
	public function assignRoles($roles, array $data = [], bool $force = false) {
		$data['semester_id'] = isset($data['semester_id']) ? ($data['semester_id'] === -1 ? null : $data['semester_id']) : Semester::getThisSemester()->id;
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

	/**
	 * Permet de modifier un ou plusieurs roles attribués en fonction des données fournis
	 * @param  string/array/Collection  $roles
	 * @param  array   $data    Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param  array   $updatedData    Possibilité d'affecter role_id, semester_id, validated_by, user_id
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function updateRoles($roles, array $data = [], array $updatedData = [], bool $force = false) {
		$data['semester_id'] = isset($data['semester_id']) ? ($data['semester_id'] === -1 ? null : $data['semester_id']) : Semester::getThisSemester()->id;
		$updatedData['semester_id'] = isset($updatedData['semester_id']) ? ($updatedData['semester_id'] === -1 ? null : $updatedData['semester_id']) : Semester::getThisSemester()->id;
		$updatedRoles = [];

		if (isset($updatedData['validated_by']))
			$manageableRoles = $this->getUserRoles($updatedData['validated_by']);

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

	/**
	 * Permet de supprimer un ou plusieurs roles attribués en fonction des données fournis
	 * @param  string/array/Collection  $roles
	 * @param  array   $data    Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param  int 	   $removed_by   Personne demandant la suppression
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function removeRoles($roles, array $data = [], int $removed_by = null, bool $force = false) {
		$data['semester_id'] = isset($data['semester_id']) ? ($data['semester_id'] === -1 ? null : $data['semester_id']) : Semester::getThisSemester()->id;
		$delRoles = [];

		if ($removed_by !== null)
			$manageableRoles = $this->getUserRoles($removed_by);

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

	/**
	 * Permet de synchroniser (tout supprimer et assigner de nouveaux) un ou plusieurs roles en fonction des données fournis
	 * @param  string/array/Collection  $roles
	 * @param  array   $data    Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param  int 	   $removed_by   Personne demandant la suppression
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
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

	/**
	 * Regarde si un role parmi la liste a été donné ou non
	 * @param  string/array/Collection  $roles
	 * @param  array   $data    Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @return boolean
	 */
    public function hasOneRole($roles, array $data = []) {
		$data['semester_id'] = isset($data['semester_id']) ? ($data['semester_id'] === -1 ? null : $data['semester_id']) : Semester::getThisSemester()->id;

        return Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id')->intersect($this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? null)->pluck('id'))->isNotEmpty();
    }

	/** Regarde si tous les roles parmi la liste existe ou non
	 * @param  string/array/Collection  $members
	 * @param  array   $data    Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @return boolean
	 */
    public function hasAllRoles($roles, array $data = []) {
		$data['semester_id'] = isset($data['semester_id']) ? ($data['semester_id'] === -1 ? null : $data['semester_id']) : Semester::getThisSemester()->id;

        return Role::getRoles(stringToArray($roles), $this->getTable())->pluck('id')->diff($this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'])->pluck('id'))->isEmpty();
    }

	/**
	 * Récupérer les rôles assignés d'une personne
	 * @param  int  $user_id
	 * @param  int/false $semester_id
	 * @param  boolean $needToBeValidated
	 */
	public function getUserAssignedRoles(int $user_id = null, $semester_id = null, $needToBeValidated = true) {
		$semester_id = isset($semester_id) ? ($semester_id === -1 ? null : $semester_id) : Semester::getThisSemester()->id;
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

	/**
	 * Récupérer les rôles de cette instance ou de celui sur les users assignés et hérités d'une personne
	 * @param  int  $user_id     [description]
	 * @param  int/false $semester_id
	 */
	public function getUserRoles(int $user_id = null, $semester_id = null) {
		$semester_id = isset($semester_id) ? ($semester_id === -1 ? null : $semester_id) : Semester::getThisSemester()->id;
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

	/**
	 * Override de la méthode du trait hasPermissions: Récupérer les permissions de cette instance ou de celui sur les users assignés et hérités d'une personne
	 * @param  int  $user_id     [description]
	 * @param  int/false $semester_id
	 */
	public function getUserPermissions(int $user_id = null, $semester_id = null) {
		$permissions = $this->getUserPermissionsFromHasPermissions($user_id, $semester_id);

		foreach ($this->getUserRoles($user_id, $semester_id)->pluck('id') as $role_id)
			$permissions = $permissions->merge(Role::find($role_id)->permissions);

		return $permissions;
	}
}

<?php

namespace App\Traits\Model;

use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Models\Permission;
use App\Models\Semester;
use App\Models\User;

trait HasPermissions
{
	/**
	 * Méthode appelée au chargement du trait
	 */
    public static function bootHasPermissions() {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->permissions()->detach();
        });
    }

	/**
	 * Récupération du nom de la table de relation
	 * @return string
	 */
	protected function getPermissionRelationTable() {
		return $this->permissionRelationTable ?? $this->getTable().'_permissions';
	}

	/**
	 * Liste des permissions attribués
	 */
	public function permissions() {
		return $this->belongsToMany(Permission::class, $this->getPermissionRelationTable());
	}

	/**
	 * Permet d'assigner une ou plusieures permissions attribués en fonction des données fournis
	 * @param  string/array/Collection  $permissions
	 * @param  array   $data    Possibilité d'affecter permission_id, semester_id, validated_by, user_id
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
	public function assignPermissions($permissions, array $data = [], bool $force = false) {
		$data['semester_id'] = $data['semester_id'] ?? Semester::getThisSemester()->id;
		$addPermissions = [];

		if (isset($data['validated_by']) || \Auth::id())
			$manageablePermissions = $this->getUserPermissions($data['validated_by'] ?? \Auth::id());

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable().'-'.$this->id) as $permission) {
			if (!$force) {
				if ($permission->limited_at !== null) {
					$users = $permission->users()->wherePivotIn('semester_id', [0, $data['semester_id']])->wherePivot('validated_by', '!=', null);

					if ($users->count() >= $permission->limited_at)
						throw new PortailException('Le nombre de personnes ayant cette permission a été dépassé. Limité à '.$permission->limited_at);
				}

				if (isset($data['validated_by']) || \Auth::id()) {
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

	/**
	 * Permet de modifier une ou plusieures permissions attribués en fonction des données fournis
	 * @param  string/array/Collection  $permissions
	 * @param  array   $data    Possibilité d'utiliser permission_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param  array   $updatedData    Possibilité d'affecter permission_id, semester_id, validated_by, user_id
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function updatePermissions($permissions, array $data = [], array $updatedData = [], bool $force = false) {
		$data['semester_id'] = $data['semester_id'] ?? Semester::getThisSemester()->id;
		$updatedData['semester_id'] = $updatedData['semester_id'] ?? Semester::getThisSemester()->id;
		$updatedPermissions = [];

		if (isset($updatedData['validated_by']) || \Auth::id())
			$manageablePermissions = $this->getUserPermissions($updatedData['validated_by'] ?? \Auth::id());

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable().'-'.$this->id) as $permission) {
			if (!$force && (isset($updatedData['validated_by']) || \Auth::id())) {
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

	/**
	 * Permet de supprimer une ou plusieures permissions attribués en fonction des données fournis
	 * @param  string/array/Collection  $permissions
	 * @param  array   $data    Possibilité d'utiliser permission_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param   	   $removed_by   Personne demandant la suppression
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function removePermissions($permissions, array $data = [], $removed_by = null, bool $force = false) {
		$data['semester_id'] = $data['semester_id'] ?? Semester::getThisSemester()->id;
		$delPermissions = [];
        $removed_by = $removed_by ?? \Auth::id();

		if ($removed_by !== null)
			$manageablePermissions = $this->getUserPermissions($removed_by);

		foreach (Permission::getPermissions(stringToArray($permissions), $this->getTable().'-'.$this->id) as $permission) {
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

	/**
	 * Permet de synchroniser (tout supprimer et assigner de nouveaux) une ou plusieures permissions en fonction des données fournis
	 * @param  string/array/Collection  $permissions
	 * @param  array   $data    Possibilité d'utiliser permission_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param   	   $removed_by   Personne demandant la suppression
	 * @param  boolean $force   Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function syncPermissions($permissions, array $data = [], $removed_by = null, bool $force = false) {
		$currentPermissions = $this->getUserAssignedPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? Semester::getThisSemester()->id, false)->pluck('id');
		$permissions = Permission::getPermissions(stringToArray($permissions), $this->getTable().'-'.$this->id)->pluck('id');
		$intersectedPermissions = $currentPermissions->intersect($permissions);

		$oldData = [];
		if ($data['semester_id'] ?? false)
			$oldData['semester_id'] = $data['semester_id'];
		if ($data['user_id'] ?? false)
			$oldData['user_id'] = $data['user_id'];

        return $this->assignPermissions($permissions->diff($currentPermissions), $data, $force)->updatePermissions($intersectedPermissions, $oldData, $data, $force)->removePermissions($currentPermissions->diff($permissions), $data, $removed_by, $force);
    }

	/**
	 * Regarde si une permission parmi la liste a été donnée ou non
	 * @param  string/array/Collection  $permissions
	 * @param  array   $data    Possibilité d'utiliser permission_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @return boolean
	 */
    public function hasOnePermission($permissions, array $data = []) {
        return Permission::getPermissions(stringToArray($permissions), $this->getTable().'-'.$this->id)->pluck('id')->intersect($this->getUserPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? Semester::getThisSemester()->id)->pluck('id'))->isNotEmpty();
    }

	/** Regarde si toutes les permissions parmi la liste existe ou non
	 * @param  string/array/Collection  $members
	 * @param  array   $data    Possibilité d'utiliser permission_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @return boolean
	 */
    public function hasAllPermissions($permissions, array $data = []) {
        return Permission::getPermissions(stringToArray($permissions), $this->getTable().'-'.$this->id)->pluck('id')->diff($this->getUserPermissions($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? Semester::getThisSemester()->id)->pluck('id'))->isEmpty();
    }

	/**
	 * Récupérer les permissions assignées d'une personne
	 * @param  $user_id
	 * @param  $semester_id
	 * @param  boolean $needToBeValidated
	 */
	public function getUserAssignedPermissions($user_id = null, $semester_id = null, $needToBeValidated = true) {
		$semester_id = $semester_id ?? Semester::getThisSemester()->id;
		$permissions = $this->permissions();

		if ($permissions === null)
		return new Collection;

		if ($this->getTable() !== 'users' || $user_id !== null)
			$permissions = $permissions->wherePivot('user_id', $user_id);

		$relatedTable = $this->getPermissionRelationTable();

		$permissions = $permissions->wherePivotIn('semester_id', [0, $semester_id]);

		if ($needToBeValidated)
			$permissions = $permissions->wherePivot('validated_by', '!=', null);

		return $permissions->get();
	}

	/**
	 * Récupérer les permissions de cette instance ou de celui sur les users assignés et hérités d'une personne
	 * @param  $user_id    
	 * @param  $semester_id
	 */
	public function getUserPermissions($user_id = null, $semester_id = null) {
		return $this->getUserAssignedPermissions($user_id, $semester_id);
	}
}

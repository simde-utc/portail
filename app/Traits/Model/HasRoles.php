<?php

namespace App\Traits\Model;

use App\Exceptions\PortailException;
use App\Traits\Model\HasPermissions;
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
	 *
	 * @return string
	 */
	public function getRoleRelationTable() {
		return $this->roleRelationTable ?? $this->getTable().'_roles';
	}

	/**
	 * Liste des roles attribués
	 */
	public function roles() {
	    return $this->belongsToMany(Role::class, $this->getRoleRelationTable());
	}

	/**
	 * Permet d'assigner un ou plusieurs roles attribués en fonction des données fournis
	 *
	 * @param string|array|Illuminate\Database\Eloquent\Collection $roles
	 * @param array $data 		Possibilité d'affecter role_id, semester_id, validated_by, user_id
	 * @param bool $force 		Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
	public function assignRoles($roles, array $data = [], bool $force = false) {
		$data['semester_id'] = array_key_exists('semester_id', $data) ? $data['semester_id'] : Semester::getThisSemester()->id;
		$addRoles = [];

		if (isset($data['validated_by']) || \Auth::id())
			$manageableRoles = $this->getUserRoles($data['validated_by'] ?? \Auth::id());

		$nbr = @count($roles) ?? 1;
		$roles = Role::getRoles(stringToArray($roles), $this);

		if (count($roles) !== $nbr)
			throw new PortailException('Certains rôles donnés n\'ont pas pu être trouvé');

		foreach ($roles as $role) {
			if (!$force) {
				if ($role->limited_at !== null) {
					$users = $role->users()->wherePivotIn('semester_id', [0, $data['semester_id']])->wherePivot('validated_by', '!=', null);

					if ($users->count() >= $role->limited_at)
						throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. Limité à '.$role->limited_at);
				}

				if (isset($data['validated_by']) || \Auth::id()) {
					if (!$manageableRoles->contains('id', $role->id) && !$manageableRoles->contains('type', config('portail.roles.admin.'.$this->getTable())))
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
	 *
	 * @param string|array|Illuminate\Database\Eloquent\Collection $roles
	 * @param array $data 			Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param array $updatedData 	Possibilité d'affecter role_id, semester_id, validated_by, user_id
	 * @param bool $force 			Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function updateRoles($roles, array $data = [], array $updatedData = [], bool $force = false) {
		$data['semester_id'] = $data['semester_id'] ?? Semester::getThisSemester()->id;
		$updatedData['semester_id'] = $updatedData['semester_id'] ?? Semester::getThisSemester()->id;
		$updatedRoles = [];

		if (isset($updatedData['validated_by']) || \Auth::id())
			$manageableRoles = $this->getUserRoles($updatedData['validated_by'] ?? \Auth::id());

		$nbr = @count($roles) ?? 1;
		$roles = Role::getRoles(stringToArray($roles), $this->getTable().'-'.$this->id);

		if (count($roles) !== $nbr)
  			throw new PortailException('Certains rôles donnés n\'ont pas pu être trouvé');

		foreach ($roles as $role) {
			if (!$force && (isset($updatedData['validated_by']) || \Auth::id())) {
				if (!$manageableRoles->contains('id', $role->id) && (!$manageableRoles->contains('type', config('portail.roles.admin.'.$this->getTable())) || $role->allChildren->contains('type', config('portail.roles.admin.'.$this->getTable()))))
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
	 *
	 * @param string|array|Illuminate\Database\Eloquent\Collection $roles
	 * @param array $data 		Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param $removed_by 	Personne demandant la suppression
	 * @param bool $force   	Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function removeRoles($roles, array $data = [], $removed_by = null, bool $force = false) {
		$data['semester_id'] = $data['semester_id'] ?? Semester::getThisSemester()->id;
		$delRoles = [];
		$removed_by = $removed_by ?? \Auth::id();

		if ($removed_by !== null)
			$manageableRoles = $this->getUserRoles($removed_by);

		$nbr = @count($roles) ?? 1;
		$roles = Role::getRoles(stringToArray($roles), $this->getTable().'-'.$this->id);

		if (count($roles) !== $nbr)
  			throw new PortailException('Certains rôles donnés n\'ont pas pu être trouvé');

		foreach ($roles as $role) {
			if (!$force && $removed_by !== null) {
				if (!$manageableRoles->contains('id', $role->id) && (!$manageableRoles->contains('type', config('portail.roles.admin.'.$this->getTable())) || $role->allChildren->contains('type', config('portail.roles.admin.'.$this->getTable()))))
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
	 *
	 * @param string|array|Illuminate\Database\Eloquent\Collection $roles
	 * @param array $data 		Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @param $removed_by 	Personne demandant la suppression
	 * @param bool $force 		Permet de sauter les sécurités d'ajout (à utiliser avec prudence)
	 */
    public function syncRoles($roles, array $data = [], $removed_by = null, bool $force = false) {
		$currentRoles = $this->getUserAssignedRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? 0, false)->pluck('id');
		$roles = Role::getRoles(stringToArray($roles), $this->getTable().'-'.$this->id)->pluck('id');
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
	 *
	 * @param string|array|Illuminate\Database\Eloquent\Collection $roles
	 * @param array $data 	Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @return bool
	 */
    public function hasOneRole($roles, array $data = []) {
        return Role::getRoles(stringToArray($roles), $this->getTable().'-'.$this->id)->pluck('id')->intersect($this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? Semester::getThisSemester()->id)->pluck('id'))->isNotEmpty();
    }

	/**
	 * Regarde si tous les roles parmi la liste existe ou non
	 *
	 * @param string|array|Illuminate\Database\Eloquent\Collection $members
	 * @param array $data 	Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres
	 * @return bool
	 */
    public function hasAllRoles($roles, array $data = []) {
        return Role::getRoles(stringToArray($roles), $this->getTable().'-'.$this->id)->pluck('id')->diff($this->getUserRoles($data['user_id'] ?? $this->user_id ?? $this->id, $data['semester_id'] ?? Semester::getThisSemester()->id)->pluck('id'))->isEmpty();
    }

	/**
	 * Récupérer les rôles assignés d'une personne
	 *
	 * @param $user_id
	 * @param $semester_id
	 * @param bool $needToBeValidated
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getUserAssignedRoles($user_id = null, $semester_id = null, $needToBeValidated = true) {
		$semester_id = $semester_id ?? Semester::getThisSemester()->id;
		$roles = $this->roles();

		if ($roles === null)
			return new Collection;

		if ($user_id !== null) {
			if ($this->getTable() === 'users')
				$roles = User::find($user_id)->roles();
			else
				$roles = $roles->wherePivot('user_id', $user_id);
		}

		$roles = $roles->wherePivotIn('semester_id', [0, $semester_id]);

		if ($needToBeValidated)
			$roles = $roles->wherePivot('validated_by', '!=', null);

		return $roles->get();
	}

	/**
	 * Récupérer les rôles de cette instance ou de celui sur les users assignés et hérités d'une personne
	 *
	 * @param $user_id
	 * @param $semester_id
	 */
	public function getUserRoles($user_id = null, $semester_id = null) {
		$semester_id = $semester_id ?? Semester::getThisSemester()->id;
		$roles = collect();

		foreach ($this->getUserAssignedRoles($user_id, $semester_id) as $role) {
			$roles->push($role);

			$roles = $roles->merge($role->allChildren());
			$role->makeHidden('children');
		}

		// On ajoute les rôles de l'utilisateur sur le système
		if ($this->getTable() !== 'users' && $user_id) {
			foreach (User::find($user_id)->getUserRoles(null, $semester_id) as $userRole)
				$roles->push($userRole);
		}

		return $roles->unique('id');
	}

	/**
	 * Override de la méthode du trait hasPermissions: Récupérer les permissions de cette instance ou de celui sur les users assignés et hérités d'une personne
	 *
	 * @param $user_id
	 * @param $semester_id
	 */
	public function getUserPermissions($user_id = null, $semester_id = null) {
		$permissions = $this->getUserPermissionsFromHasPermissions($user_id, $semester_id);

		foreach ($this->getUserRoles($user_id, $semester_id)->pluck('id') as $role_id)
			$permissions = $permissions->merge(Role::find($role_id)->permissions);

		return $permissions;
	}

	// Par défaut, un role n'est pas supprimable s'il a déjà été assigné
	public function isRoleDeletable($role) {
		return $role->{\ModelResolver::getCategory($role->owned_by_type)}()->count() === 0;
	}

	public function isRoleForIdDeletable($role, $id) {
		return $this->isRoleDeletable($role);
	}

	public function beforeDeletingRole($role) {
		return $role->{\ModelResolver::getCategory($role->owned_by_type)}()->detach();
	}
}

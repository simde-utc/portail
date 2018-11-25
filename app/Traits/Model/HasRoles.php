<?php
/**
 * Ajoute la gestion des rôles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

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
        HasPermissions::getUserPermissions as getUserPermissionsFromHasPermissions;
    }

    /**
     * Méthode appelée au chargement du class.
     *
     * @return void
     */
    public static function bootHasRoles()
    {
        static::deleting(function ($model) {
            // Si on souhaite supprimer la ressources, on supprime les membres associés.
            if (!method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                $model->roles()->detach();
            }
        });
    }

    /**
     * Récupération du nom de la table de relation.
     *
     * @return string
     */
    public function getRoleRelationTable()
    {
        return ($this->roleRelationTable ?? $this->getTable().'_roles');
    }

    /**
     * Liste des roles attribués.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, $this->getRoleRelationTable());
    }

    /**
     * Permet d'assigner un ou plusieurs roles attribués en fonction des données fournis.
     *
     * @param string|array|Illuminate\Database\Eloquent\Collection $roles
     * @param array                                                $data  Possibilité d'affecter role_id, semester_id, validated_by, user_id.
     * @param boolean                                              $force Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function assignRoles($roles, array $data=[], bool $force=false)
    {
        $data['semester_id'] = array_key_exists('semester_id', $data) ? $data['semester_id'] : Semester::getThisSemester()->id;
        $addRoles = [];
        $manageableRoles = collect();

        if (isset($data['validated_by']) || \Auth::id()) {
            $manageableRoles = $this->getUserRoles(($data['validated_by'] ?? \Auth::id()));
        }

        $nbr = @(count($roles) ?? 1);
        $roles = Role::getRoles(stringToArray($roles), $this);

        if (count($roles) !== $nbr) {
            throw new PortailException('Certains rôles donnés n\'ont pas pu être trouvé');
        }

        foreach ($roles as $role) {
            if (!$force) {
                if ($role->limited_at !== null) {
                    $users = $role->users()->wherePivotIn('semester_id', [0, $data['semester_id']])
                        ->wherePivot('validated_by', '!=', null);

                    if ($users->count() >= $role->limited_at) {
                        throw new PortailException('Le nombre de personnes ayant ce role a été dépassé. \
                            Limité à '.$role->limited_at);
                    }
                }

                if (isset($data['validated_by']) || \Auth::id()) {
                    if (!$manageableRoles->contains('id', $role->id)
                        && !$manageableRoles->contains('type', config('portail.roles.admin.'.$this->getTable()))) {
                        throw new PortailException('La personne demandant la validation n\'est pas habilitée à donner \
                            ce rôle: '.$role->name);
                    }
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
     * Permet de modifier un ou plusieurs roles attribués en fonction des données fournis.
     *
     * @param string|array|Illuminate\Database\Eloquent\Collection $roles
     * @param array                                                $data        Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres.
     * @param array                                                $updatedData Possibilité d'affecter role_id, semester_id, validated_by, user_id.
     * @param boolean                                              $force       Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function updateRoles($roles, array $data=[], array $updatedData=[], bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $updatedData['semester_id'] = ($updatedData['semester_id'] ?? Semester::getThisSemester()->id);
        $updatedRoles = [];
        $manageableRoles = collect();

        if (isset($updatedData['validated_by']) || \Auth::id()) {
            $manageableRoles = $this->getUserRoles(($updatedData['validated_by'] ?? \Auth::id()));
        }

        $nbr = @(count($roles) ?? 1);
        $roles = Role::getRoles(stringToArray($roles), $this);

        if (count($roles) !== $nbr) {
            throw new PortailException('Certains rôles donnés n\'ont pas pu être trouvé');
        }

        foreach ($roles as $role) {
            if (!$force && (isset($updatedData['validated_by']) || \Auth::id())) {
                if (!$manageableRoles->contains('id', $role->id)
                    && (!$manageableRoles->contains('type', config('portail.roles.admin.'.$this->getTable()))
                        || $role->allChildren->contains('type', config('portail.roles.admin.'.$this->getTable())))) {
                    throw new PortailException('La personne demandant la validation n\'est pas habilitée à modifier \
                        ce rôle: '.$role->name);
                }
            }

            array_push($updatedRoles, $role->id);
        }

        $toUpdate = $this->roles()->withTimestamps();

        foreach ($data as $key => $value) {
            $toUpdate->wherePivot($key, $value);
        }

        try {
            foreach ($updatedRoles as $updatedRole) {
                $toUpdate->updateExistingPivot($updatedRole, $updatedData);
            }
        } catch (\Exception $e) {
            throw new PortailException('Les données d\'un role ne peuvent être modifiées');
        }

        return $this;
    }

    /**
     * Permet de supprimer un ou plusieurs roles attribués en fonction des données fournis.
     *
     * @param string|array|Illuminate\Database\Eloquent\Collection $roles
     * @param array                                                $data       Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres.
     * @param string                                               $removed_by Personne demandant la suppression.
     * @param boolean                                              $force      Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function removeRoles($roles, array $data=[], string $removed_by=null, bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $delRoles = [];
        $removed_by = ($removed_by ?? \Auth::id());
        $manageableRoles = collect();

        if ($removed_by !== null) {
            $manageableRoles = $this->getUserRoles($removed_by);
        }

        $nbr = @(count($roles) ?? 1);
        $roles = Role::getRoles(stringToArray($roles), $this);

        if (count($roles) !== $nbr) {
            throw new PortailException('Certains rôles donnés n\'ont pas pu être trouvé');
        }

        foreach ($roles as $role) {
            if (!$force && $removed_by !== null) {
                if (!$manageableRoles->contains('id', $role->id)
                    && (!$manageableRoles->contains('type', config('portail.roles.admin.'.$this->getTable()))
                        || $role->allChildren->contains('type', config('portail.roles.admin.'.$this->getTable())))) {
                    throw new PortailException('La personne demandant la suppression n\'est pas habilitée à \
                        retirer ce rôle: '.$role->name);
                }
            }

            array_push($delRoles, $role->id);
        }

        $toDetach = $this->roles();

        foreach ($data as $key => $value) {
            $toDetach->wherePivot($key, $value);
        }

        try {
            $toDetach->detach($delRoles);
        } catch (\Exception $e) {
            throw new PortailException('Une erreur a été recontrée à la suppression d\'un role utilisateur');
        }

        return $this;
    }

    /**
     * Permet de synchroniser (tout supprimer et assigner de nouveaux) un ou plusieurs roles en fonction des données fournis.
     *
     * @param string|array|Illuminate\Database\Eloquent\Collection $roles
     * @param array                                                $data       Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres.
     * @param string                                               $removed_by Personne demandant la suppression.
     * @param boolean                                              $force      Permet de sauter les sécurités d'ajout (à utiliser avec prudence).
     * @return mixed
     */
    public function syncRoles($roles, array $data=[], string $removed_by=null, bool $force=false)
    {
        $currentRoles = $this->getUserAssignedRoles(($data['user_id'] ?? $this->user_id ?? $this->id),
            ($data['semester_id'] ?? 0), false)->pluck('id');
        $roles = Role::getRoles(stringToArray($roles), $this)->pluck('id');
        $intersectedRoles = $currentRoles->intersect($roles);
        $oldData = [];

        if (($data['semester_id'] ?? false)) {
            $oldData['semester_id'] = $data['semester_id'];
        }

        if (($data['user_id'] ?? false)) {
            $oldData['user_id'] = $data['user_id'];
        }

        return $this->assignRoles($roles->diff($currentRoles), $data, $force)
            ->updateRoles($intersectedRoles, $oldData, $data, $force)
            ->removeRoles($currentRoles->diff($roles), $data, $removed_by, $force);
    }

    /**
     * Regarde si un role parmi la liste a été donné ou non.
     *
     * @param string|array|Illuminate\Database\Eloquent\Collection $roles
     * @param array                                                $data  Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres.
     * @return boolean
     */
    public function hasOneRole($roles, array $data=[])
    {
        return Role::getRoles(stringToArray($roles), $this)->pluck('id')
            ->intersect($this->getUserRoles(($data['user_id'] ?? $this->user_id ?? $this->id),
            ($data['semester_id'] ?? Semester::getThisSemester()->id))->pluck('id'))->isNotEmpty();
    }

    /**
     * Regarde si tous les roles parmi la liste existe ou non.
     *
     * @param string|array|Illuminate\Database\Eloquent\Collection $roles
     * @param array                                                $data  Possibilité d'utiliser role_id, semester_id, validated_by, user_id pour matcher un member ou plusieurs membres.
     * @return boolean
     */
    public function hasAllRoles($roles, array $data=[])
    {
        return Role::getRoles(stringToArray($roles), $this)->pluck('id')
            ->diff($this->getUserRoles(($data['user_id'] ?? $this->user_id ?? $this->id),
            ($data['semester_id'] ?? Semester::getThisSemester()->id))->pluck('id'))->isEmpty();
    }

    /**
     * Récupérer les rôles assignés d'une personne.
     *
     * @param string  $user_id
     * @param string  $semester_id
     * @param boolean $needToBeValidated
     * @return Collection
     */
    public function getUserAssignedRoles(string $user_id=null, string $semester_id=null, bool $needToBeValidated=true)
    {
        $semester_id = ($semester_id ?? Semester::getThisSemester()->id);
        $roles = $this->roles();

        if ($roles === null) {
            return collect();
        }

        if ($user_id !== null) {
            if (get_class($this) === User::class) {
                $roles = User::find($user_id)->roles();
            } else {
                $roles = $roles->wherePivot('user_id', $user_id);
            }
        }

        $roles = $roles->wherePivotIn('semester_id', [0, $semester_id]);

        if ($needToBeValidated) {
            $roles = $roles->wherePivot('validated_by', '!=', null);
        }

        return $roles->withPivot(['validated_by', 'semester_id'])->get();
    }

    /**
     * Récupérer les rôles de cette instance ou de celui sur les users assignés et hérités d'une personne.
     *
     * @param string $user_id
     * @param string $semester_id
     * @return Collection
     */
    public function getUserRoles(string $user_id=null, string $semester_id=null)
    {
        $semester_id = ($semester_id ?? Semester::getThisSemester()->id);
        $roles = collect();

        foreach ($this->getUserAssignedRoles($user_id, $semester_id) as $role) {
            $roles->push($role);

            $roles = $roles->merge($role->allChildren());
            $role->makeHidden('children');
        }

        // On ajoute les rôles de l'utilisateur sur le système.
        if ($this->getTable() !== 'users' && $user_id) {
            foreach (User::find($user_id)->getUserRoles(null, $semester_id) as $userRole) {
                $roles->push($userRole);
            }
        }

        return $roles;
    }

    /**
     * Récupération des rôles d'un utilisateur à partir de ses permissions.
     *
     * @param  string $user_id
     * @param  string $semester_id
     * @return mixed
     */
    public function getUserPermissionsFromRoles(string $user_id=null, string $semester_id=null)
    {
        $permissions = collect();

        foreach ($this->getUserRoles($user_id, $semester_id) as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions;
    }

    /**
     * Override de la méthode du class hasPermissions: Récupérer les permissions de cette instance ou de celui sur les users assignés et hérités d'une personne.
     *
     * @param string $user_id
     * @param string $semester_id
     * @return mixed
     */
    public function getUserPermissions(string $user_id=null, string $semester_id=null)
    {
        $permissions = $this->getUserPermissionsFromHasPermissions($user_id, $semester_id);
        $permissions = $permissions->merge($this->getUserPermissionsFromRoles($user_id, $semester_id));

        return $permissions;
    }

    /**
     * Indique si un rôle est supprimable.
     * Par défaut, un role n'est pas supprimable s'il a déjà été assigné.
     *
     * @param  Role $role
     * @return boolean
     */
    public function isRoleDeletable(Role $role)
    {
        return $role->{\ModelResolver::getCategory($role->owned_by_type)}()->count() === 0;
    }

    /**
     * Indique si un rôle est supprimable pour un id de cette instance.
     *
     * @param  Role   $role
     * @param  string $model_id
     * @return boolean
     */
    public function isRoleForIdDeletable(Role $role, string $model_id)
    {
        return $this->isRoleDeletable($role);
    }

    /**
     * Action avant la suppression du rôle.
     *
     * @param  Role $role
     * @return boolean
     */
    public function beforeDeletingRole(Role $role)
    {
        return $role->{\ModelResolver::getCategory($role->owned_by_type)}()->detach();
    }
}

<?php
/**
 * Add a permission management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Models\Permission;
use App\Models\Semester;
use App\Models\User;

trait HasPermissions
{
    /**
     * Method called at the class loading.
     *
     * @return void
     */
    public static function bootHasPermissions()
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                return;
            }

            $model->permissions()->detach();
        });
    }

    /**
     * Relation table's name retrievement.
     *
     * @return string
     */
    protected function getPermissionRelationTable()
    {
        return ($this->permissionRelationTable ?? $this->getTable().'_permissions');
    }

    /**
     * List all attributed permissions.
     *
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, $this->getPermissionRelationTable());
    }

    /**
     * Assign one or several permissions depending on the given data.
     *
     * @param  string|array|Collection $permissions
     * @param  array                   $data        Possibility to affect permission_id, semester_id, validated_by_id, user_id.
     * @param  boolean                 $force       Enable to by-pass addition security (to use with caution).
     * @return mixed
     */
    public function assignPermissions($permissions, array $data=[], bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $addPermissions = [];
        $manageablePerms = collect();

        if (isset($data['validated_by_id']) || \Auth::id()) {
            $manageablePerms = $this->getUserPermissions(($data['validated_by_id'] ?? \Auth::id()));
        }

        foreach (Permission::getPermissions(stringToArray($permissions), $this) as $permission) {
            if (!$force) {
                if (isset($data['validated_by_id']) || \Auth::id()) {
                    if (!$manageablePerms->contains('id', $permission->id)) {
                        throw new PortailException('La personne demandant la validation n\'est pas habilitée à \
                            donner cette permission: '.$permission->name);
                    }
                }
            }

            $addPermissions[$permission->id] = $data;
        }

        try {
            $this->permissions()->withTimestamps()->attach($addPermissions);
        } catch (\Exception $e) {
            throw new PortailException('Une des personnes possède déjà trop de permissions');
        }

        return $this;
    }

    /**
     * Modify one or several permissions depending on the given data.
     *
     * @param  string|array|Collection $permissions
     * @param  array                   $data        Possibility to use permission_id, semester_id, validated_by_id and user_id to match a member or several members.
     * @param  array                   $updatedData Possibility to affect permission_id, semester_id, validated_by_id and user_id.
     * @param  boolean                 $force       Enable to by-pass addition security (to use with caution).
     * @return mixed
     */
    public function updatePermissions($permissions, array $data=[], array $updatedData=[], bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $updatedData['semester_id'] = ($updatedData['semester_id'] ?? Semester::getThisSemester()->id);
        $updatedPermissions = [];
        $manageablePerms = collect();

        if (isset($updatedData['validated_by_id']) || \Auth::id()) {
            $manageablePerms = $this->getUserPermissions(($updatedData['validated_by_id'] ?? \Auth::id()));
        }

        foreach (Permission::getPermissions(stringToArray($permissions), $this) as $permission) {
            if (!$force && (isset($updatedData['validated_by_id']) || \Auth::id())) {
                if (!$manageablePerms->contains('id', $permission->id)) {
                    throw new PortailException('La personne demandant la validation n\'est pas habilitée à \
                        modifier cette permission: '.$permission->name);
                }
            }

            $updatedPermissions = $permission->id;
        }

        $toUpdate = $this->permissions()->withTimestamps();

        foreach ($data as $key => $value) {
            $toUpdate->wherePivot($key, $value);
        }

        try {
            foreach ($updatedPermissions as $updatedPermission) {
                $toUpdate->updateExistingPivot($updatedPermission, $updatedData);
            }
        } catch (\Exception $e) {
            throw new PortailException('Les données d\'une permission ne peuvent être modifiées');
        }

        return $this;
    }

    /**
     * Delete one or several permissions depending on the given data.
     *
     * @param  string|array|Collection $permissions
     * @param  array                   $data        Possibility to use permission_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @param  string                  $removed_by  Personne demandant la suppression.
     * @param  boolean                 $force       Enable to by-pass addition security (to use with caution).
     * @return mixed
     */
    public function removePermissions($permissions, array $data=[], string $removed_by=null, bool $force=false)
    {
        $data['semester_id'] = ($data['semester_id'] ?? Semester::getThisSemester()->id);
        $delPermissions = [];
        $manageablePerms = collect();
        $removed_by = ($removed_by ?? \Auth::id());

        if ($removed_by !== null) {
            $manageablePerms = $this->getUserPermissions($removed_by);
        }

        foreach (Permission::getPermissions(stringToArray($permissions), $this) as $permission) {
            if (!$force && $removed_by !== null) {
                if (!$manageablePerms->contains('id', $permission->id)) {
                    throw new PortailException('La personne demandant la suppression n\'est pas habilitée à \
                        retirer cette permission: '.$permission->name);
                }
            }

            $delPermissions[] = $permission->id;
        }

        $toDetach = $this->permissions();

        foreach ($data as $key => $value) {
            $toDetach->wherePivot($key, $value);
        }

        try {
            $toDetach->detach($delPermissions);
        } catch (\Exception $e) {
            throw new PortailException('Une erreur a été recontrée à la suppression d\'une permission');
        }

        return $this;
    }

    /**
     * Synchronize (Delete all and assigns new permissions) one or several permissions depending on the given data.
     *
     * @param  string|array|Collection $permissions
     * @param  array                   $data        Possibility to use permission_id, semester_id, validated_by_id, user_id pour matcher un member ou plusieurs membres.
     * @param  string                  $removed_by  Personne demandant la suppression.
     * @param  boolean                 $force       Enable to by-pass addition security (to use with caution).
     * @return mixed
     */
    public function syncPermissions($permissions, array $data=[], string $removed_by=null, bool $force=false)
    {
        $currentPermissions = $this->getUserAssignedPermissions(($data['user_id'] ?? $this->user_id ?? $this->id),
            ($data['semester_id'] ?? Semester::getThisSemester()->id), false)->pluck('id');
        $permissions = Permission::getPermissions(stringToArray($permissions), $this)->pluck('id');
        $intersectedPerms = $currentPermissions->intersect($permissions);

        $oldData = [];
        if (($data['semester_id'] ?? false)) {
            $oldData['semester_id'] = $data['semester_id'];
        }

        if (($data['user_id'] ?? false)) {
            $oldData['user_id'] = $data['user_id'];
        }

        return $this->assignPermissions($permissions->diff($currentPermissions), $data, $force)
            ->updatePermissions($intersectedPerms, $oldData, $data, $force)
            ->removePermissions($currentPermissions->diff($permissions), $data, $removed_by, $force);
    }

    /**
     * Check if a permission in the given list has been granted or not.
     *
     * @param  string|array|Collection $permissions
     * @param  array                   $data        Possibility to use permission_id, semester_id, validated_by_id an user_id to match one or several members.
     * @return boolean
     */
    public function hasOnePermission($permissions, array $data=[])
    {
        return Permission::getPermissions(stringToArray($permissions), $this)->pluck('id')
            ->intersect($this->getUserPermissions(($data['user_id'] ?? $this->user_id ?? $this->id),
                ($data['semester_id'] ?? Semester::getThisSemester()->id))->pluck('id'))->isNotEmpty();
    }

    /**
     * Check if every permission in the list have been granted or not.
     *
     * @param  string|array|Collection $permissions
     * @param  array                   $data        Possibility to use permission_id, semester_id, validated_by_id and user_id to mactch one or several members.
     * @return boolean
     */
    public function hasAllPermissions($permissions, array $data=[])
    {
        return Permission::getPermissions(stringToArray($permissions), $this)->pluck('id')
            ->diff($this->getUserPermissions(($data['user_id'] ?? $this->user_id ?? $this->id),
            ($data['semester_id'] ?? Semester::getThisSemester()->id))->pluck('id'))->isEmpty();
    }

    /**
     * Retrieve a user assigned permissions.
     *
     * @param  string  $user_id
     * @param  string  $semester_id
     * @param  boolean $needToBeValidated
     * @return mixed
     */
    public function getUserAssignedPermissions(string $user_id=null, string $semester_id=null, bool $needToBeValidated=true)
    {
        $semester_id = ($semester_id ?? Semester::getThisSemester()->id);
        $permissions = $this->permissions();

        if ($permissions === null) {
            return collect();
        }

        if ($this->getTable() !== 'users' || $user_id !== null) {
            $permissions = $permissions->wherePivot('user_id', $user_id);
        }

        $permissions = $permissions->wherePivotIn('semester_id', [0, $semester_id]);

        if ($needToBeValidated) {
            $permissions = $permissions->wherePivot('validated_by_id', '!=', null);
        }

        return $permissions->where(function ($query) {
                return $query->where('owned_by_id', $this->id)
                    ->orWhereNull('owned_by_id');
        })->where('owned_by_type', get_class($this))
            ->withPivot(['validated_by_id', 'semester_id'])->get();
    }

    /**
     * Retrieve the permissions of the given instance or of the assigned users and his children.
     *
     * @param  string  $user_id
     * @param  string  $semester_id
     * @param  boolean $needToBeValidated
     * @return mixed
     */
    public function getUserPermissions(string $user_id=null, string $semester_id=null, bool $needToBeValidated=true)
    {
        return $this->getUserAssignedPermissions($user_id, $semester_id, $needToBeValidated);
    }
}

<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function permissions(Model $model = null, integer $semester_id = null): HasMany {
		$permissions = $this->hasMany('App\Models\Permission', 'users_permissions');

		if ($model !== null)
			$permissions->where('on_type', get_class($model))->where('on_id', $model->id);

		if ($semester_id !== null)
			$permissions->where('semester_id', $semester_id);

		return $permissions;
    }

    public function hasPermissionTo($permission, Model $model = null, integer $semester_id = null): bool {
        if (is_string($permission))
            $permission = Permission::findByType($permission);
        else if (is_int($permission))
            $permission = Permission::find($permission);

        return $this->hasDirectPermission($permission, $model, $semester_id) || $this->hasPermissionViaRole($permission, $model, $semester_id);
    }

    public function hasOnePermission($permissions, Model $model = null, integer $semester_id = null): bool {
        if (!is_array($permissions))
			return $this->hasPermissionTo($permission, $model, $semester_id);

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission, $model, $semester_id))
                return true;
        }

        return false;
    }

    public function hasAllPermissions($permissions, Model $model = null, integer $semester_id = null): bool {
        if (!is_array($permissions))
			return $this->hasPermissionTo($permission, $model, $semester_id);

        foreach ($permissions as $permission) {
            if (!$this->hasPermissionTo($permission, $model, $semester_id))
                return false;
        }

        return true;
    }

	public function hasDirectPermission($permission, Model $model = null, integer $semester_id = null): bool {
		if (is_string($permission))
		   $permission = Permission::findByType($permission);
		else if (is_int($permission))
		   $permission = Permission::find($permission);

		$contains = ['id' => $permission->id];

		if ($model !== null) {
			$contains['on_type'] = get_class($model);
			$contains['on_id'] = $model->id;
		}

		if ($semester_id !== null)
			$contains['semester_id'] = $semester_id;

	   return $this->permissions->contains($contains);
	}

    protected function hasPermissionViaRole(Permission $permission, Model $model = null, integer $semester_id = null): bool {
        return method_exists($this, 'hasRole') && $this->hasRole($permission->roles, $model, $semester_id);
    }

    public function getPermissionsViaRoles(): Collection {
        return $this->load('roles', 'roles.permissions')
            ->roles->flatMap(function ($role) {
                return $role->permissions;
            })->sort()->values();
    }

    public function getAllPermissions(): Collection {
        return $this->permissions
            ->merge($this->getPermissionsViaRoles())
            ->sort()
            ->values();
    }

    public function givePermissionTo($permissions, Model $model = null, integer $semester_id = null) {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                $permission = $this->getStoredPermission($permission);

				if ($model !== null) {
					$permission->on_type = get_class($model);
					$permission->on_id = $model->id;
				}

				if ($semester_id !== null)
					$permission->semester_id = $semester_id;

				return $permission;
            })
            ->all();

        $this->permissions()->saveMany($permissions);

        return $this;
    }

    public function syncPermissions($permissions, Model $model = null, integer $semester_id = null) {
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions, $model, $semester_id);
    }

    public function revokePermissionTo($permission, Model $model = null, integer $semester_id = null) {
        $this->permissions()->detach($this->getStoredPermission($permission, $model, $semester_id));

        $this->forgetCachedPermissions();

        return $this;
    }

    protected function getStoredPermission($permissions, Model $model = null, integer $semester_id = null) {
        if (is_numeric($permissions))
            return $this->permissions($model, $semester_id)->where('id', $permissions)->get();
        if (is_string($permissions)) {
            return app(Permission::class)->findByType($permissions);
        }

        if (is_array($permissions)) {
            return app(Permission::class)
                ->whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }

        return $permissions;
    }

    /**
     * @param \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (! $this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }

    protected function getGuardNames(): Collection
    {
        return Guard::getNames($this);
    }

    protected function getDefaultGuardName(): string
    {
        return Guard::getDefaultName($this);
    }

    /**
     * Forget the cached permissions.
     */
    public function forgetCachedPermissions()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

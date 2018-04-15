<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasPermissions;

    public static function create(array $attributes = []) {
        if (static::where('type', $attributes['type'])->first())
			throw new \Exception('Ce rôle existe déjà');

        return static::query()->create($attributes);
    }

	public static function getRole($role, $only_for = 'users') {
		return static::where('id', $role)->orWhere('type', $role)->where('only_for', $only_for)->first();
	}

    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function users(): BelongsToMany {
		return $this->belongsToMany(User::class, 'users_roles');
    }

    public function countUsers(): int {
		return $this->users()->count();
    }

    public static function findByType(string $role, bool $is_system = true): Role {
        return static::where('role', $role)->where('is_system', $is_system)->first();
    }

    public function hasPermissionTo($permission): bool {
        if (is_string($permission))
            $permission = Permission::findByType($permission);
        else if (is_int($permission))
            $permission = Permission::find($permission);

        return $this->permissions->contains('id', $permission->id);
    }

	public function givePermissionTo($permissions) {
		if (!is_array($permissions))
			$permissions = [$permissions];

		$this->permissions()->attach($permissions);

		return $this;
	}
}

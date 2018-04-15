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

	public static function find(int $id, string $only_for = 'users'): Role {
		return static::where('id', $id)->where('only_for', $only_for)->first();
	}

	public static function findByType(string $type, string $only_for = 'users'): Role {
		return static::where('type', $type)->where('only_for', $only_for)->first();
	}

	public static function getRole($role, string $only_for = 'users') {
        if (is_string($role))
            return static::findByType($role, $only_for);
        else if (is_int($role))
			return static::find($role, $only_for);
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

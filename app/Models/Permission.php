<?php

namespace App\Models;

use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Permission extends Model
{
    use HasRoles;

    public static function create(array $attributes = []) {
        if (static::where('type', $attributes['type'] ?? null)->first())
			throw new \Exception('Cette permission existe déjà');

        return static::query()->create($attributes);
    }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'users_permissions');
    }

	public static function find(int $id, bool $is_system = true) {
		return static::where('id', $id)->where('is_system', $is_system)->first();
	}

    public static function findByType(string $type, bool $is_system = true) {
		return static::where('type', $type)->where('is_system', $is_system)->first();
    }

	public static function getPermission($permissions, bool $is_system = true) {
        if (is_string($permissions))
            return static::findByType($permissions, $is_system);
        else if (is_int($permissions))
			return static::find($permissions, $is_system);
		else
			return $permission;
	}

	public static function getPermissions($permissions, bool $is_system = true) {
		if (is_array($permissions))
			return static::whereIn('id', $permissions)->orWhereIn('type', $permissions)->where('is_system', $is_system)->get();
		else
			return $permissions;
	}
}

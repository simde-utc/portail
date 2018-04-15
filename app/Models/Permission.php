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
        if (static::getPermissions()->where('type', $attributes['type'] ?? null)->first())
			throw new \Exception('Cette permission existe déjà');

        return static::query()->create($attributes);
    }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'users_permissions');
    }

    public static function findByType(string $type, bool $is_system = true): Permission {
		return static::getPermissions()->where('type', $type)->where('is_system', $is_system)->get();
    }

    protected static function getPermissions()
    {
		return new static;
		//return app(PermissionRegistrar::class)->getPermissions();
    }
}

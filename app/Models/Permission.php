<?php

namespace App\Models;

use App\Traits\Model\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Permission extends Model // TODO $must ? $fillable
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

	public static function find(int $id, string $only_for = null) {
        $permissions = static::where('id', $id);

		if ($only_for !== null) {
			$group = explode('-', $only_for)[0] ?? $only_for;

			$permissions->where(function ($query) use ($group, $only_for) {
				$query->where('only_for', $group)->orWhere('only_for', $only_for);
			});
		}

		return $permissions->first();
	}

    public static function findByType(string $type, string $only_for = null) {
        $permissions = static::where('type', $type);

		if ($only_for !== null) {
			$group = explode('-', $only_for)[0] ?? $only_for;

			$permissions->where(function ($query) use ($group, $only_for) {
				$query->where('only_for', $group)->orWhere('only_for', $only_for);
			});
		}

		return $permissions->first();
    }

	public static function getPermission($permissions, string $only_for = null) {
        if (is_string($permissions))
            return static::findByType($permissions, $is_system);
        else if (is_int($permissions))
			return static::find($permissions, $is_system);
		else
			return $permission;
	}

	public static function getPermissions($permissions, string $only_for = null) {
        $group = explode('-', $only_for)[0] ?? $only_for;

        if (is_array($permissions)) {
			$query = static::where(function ($query) use ($permissions) {
				$query->whereIn('id', $permissions)->orWhereIn('type', $permissions);
			});

			if ($only_for) {
				$query = $query->where(function ($query) use ($group, $only_for) {
					$query->where('only_for', $group)->orWhere('only_for', $only_for);
				});
			}

			return $query->get();
        }
		else if ($permissions instanceof \Illuminate\Database\Eloquent\Model)
			return collect($permissions);
		else
			return $permissions;
	}
}

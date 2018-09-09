<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasOwnerSelection;
use Illuminate\Support\Collection;

class Permission extends Model implements OwnableContract
{
		use HasMorphOwner, HasRoles;

		protected $fillable = [
			'type', 'name', 'description', 'owned_by_id', 'owned_by_type',
		];

		protected $hidden = [
			'owned_by_id', 'owned_by_type',
		];

		protected $with = [
			'owned_by',
		];

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'users_permissions');
    }

		public function owned_by() {
			return $this->morphTo('owned_by');
		}

	public static function find($id, string $only_for = null) {
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
            return static::findByType($permissions, $only_for);
        else if (is_int($permissions))
			return static::find($permissions, $only_for);
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

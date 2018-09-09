<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasOwnerSelection;
use Illuminate\Support\Collection;
use App\Interfaces\Model\CanHavePermissions;

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

		public static function find($id, CanHavePermissions $owner = null) {
			if ($owner === null)
				$owner = new User;

    	$permissions = static::where('id', $id)
				->where('owned_by_type', get_class($owner))
				->where(function ($query) use ($owner) {
					$query->whereNull('owned_by_id')
						->orWhere('owned_by_id', $owner->id);
				});

			return $permissions->first();
		}

  public static function findByType(string $type, CanHavePermissions $owner = null) {
		if ($owner === null)
			$owner = new User;

    $permissions = static::where('type', $type)
			->where('owned_by_type', get_class($owner))
			->where(function ($query) use ($owner) {
				$query->whereNull('owned_by_id')
					->orWhere('owned_by_id', $owner->id);
			});

		return $permissions->first();
  }

	public static function getPermission($permission, CanHavePermissions $owner = null) {
		if ($owner === null)
			$owner = new User;

    if (is_string($permission))
      return static::findByType($permission, $owner);
    else if (is_int($permission))
			return static::find($permission, $owner);
		else
			return $permission;
	}

	public static function getPermissions($permissions, CanHavePermissions $owner = null) {
		if ($owner === null)
			$owner = new User;
			
		if (is_array($permissions)) {
      $permissions = static::where(function ($query) use ($permissions) {
				$query->whereIn('id', $permissions)->orWhereIn('type', $permissions);
			})->where('owned_by_type', get_class($owner))
				->where(function ($query) use ($owner) {
					$query->whereNull('owned_by_id')
						->orWhere('owned_by_id', $owner->id);
				});

			return $permissions->get();
    }
		else if ($permissions instanceof Model)
			return collect($permissions);
		else
			return $permissions;
	}
}

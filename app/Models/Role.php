<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasStages;
use App\Traits\Model\HasPermissions;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasOwnerSelection;
use App\Exceptions\PortailException;
use App\Interfaces\Model\CanHaveRoles;

class Role extends Model implements OwnableContract
{
	use HasMorphOwner, HasOwnerSelection;

	protected $fillable = [
		'type', 'name', 'description', 'limited_at', 'owned_by_id', 'owned_by_type',
	];

	protected $hidden = [
		'owned_by_id', 'owned_by_type',
	];

	protected $with = [
		'owned_by',
	];

	protected $casts = [
		'limited_at' => 'integer',
	];

  public static function boot() {
		parent::boot();

    static::deleting(function ($model) {
			return $model->owned_by->beforeDeletingRole($model);
    });
  }

	public static function find($id, CanHaveRoles $owner = null) {
		if ($owner === null)
			$owner = new User;

		$roles = static::where('id', $id)
			->where('owned_by_type', get_class($owner))
			->where(function ($query) use ($owner) {
				$query->whereNull('owned_by_id')
					->orWhere('owned_by_id', $owner->id);
			});

		return $roles->first();
	}

	public static function findByType(string $type, CanHaveRoles $owner = null) {
		if ($owner === null)
			$owner = new User;

    $roles = static::where('type', $type)
			->where('owned_by_type', get_class($owner))
			->where(function ($query) use ($owner) {
				$query->whereNull('owned_by_id')
					->orWhere('owned_by_id', $owner->id);
			});

		return $roles->first();
	}

	public static function getRole($role, CanHaveRoles $owner = null) {
		if ($owner === null)
			$owner = new User;

    if (is_string($role))
      return static::findByType($role, $owner);
    else if (is_int($role))
			return static::find($role, $owner);
		else
			return $role;
	}

	public static function getRoles($roles, CanHaveRoles $owner = null) {
		if ($owner === null)
			$owner = new User;

		if (is_array($roles)) {
      $roles = static::where(function ($query) use ($roles) {
				$query->whereIn('id', $roles)->orWhereIn('type', $roles);
			})->where('owned_by_type', get_class($owner))
				->where(function ($query) use ($owner) {
					$query->whereNull('owned_by_id')
						->orWhere('owned_by_id', $owner->id);
				});

			return $roles->get();
    }
		else if ($roles instanceof Model)
			return collect($roles);
		else
			return $roles;
	}

	public static function getRoleAndItsParents($role, CanHaveRoles $owner = null) {
		if ($owner === null)
			$owner = new User;
			
		$role = static::getRole($role, $owner);

		if ($role === null)
			return null;

	 	$roles = $role->parents;
		$roles->push($role);

		return $roles;
	}

	public function permissions(): BelongsToMany {
		return $this->belongsToMany(Permission::class, 'roles_permissions');
	}

	public function children(): BelongsToMany {
		return $this->belongsToMany(Role::class, 'roles_parents', 'parent_id', 'role_id');
	}

	public function parents(): BelongsToMany {
		return $this->belongsToMany(Role::class, 'roles_parents', 'role_id', 'parent_id');
	}

	public function getOwnedByAttribute() {
		return $this->owned_by()->first() ?? resolve($this->owned_by_type);
	}

	public function owned_by() {
		return $this->morphTo('owned_by');
	}

	public function allChildren() {
		$children = collect();

		foreach ($this->children as $child) {
			$children->push($child);

			$children = $children->merge($child->allChildren());
			$child->makeHidden('children');
		}

		return $children->unique('id');
	}

	public function allParents() {
		$parents = collect();

		foreach ($this->parents as $parent) {
			$parents->push($parent);

			$parents = $parents->merge($parent->allChildren());
			$parent->makeHidden('parents');
		}

		return $parents->unique('id');
	}

	public function hasPermissionTo($permission): bool {
		if (is_string($permission))
			$permission = Permission::findByType($permission);
		else if (is_int($permission))
			$permission = Permission::find($permission);

		return $this->permissions->contains('id', $permission->id);
	}

	public function givePermissionTo($permissions) {
		$this->permissions()->withTimestamps()->attach(Permission::getPermissions(stringToArray($permissions), $this->owned_by));

		return $this;
	}

	public function removePermissionTo($permissions) {
		$this->permissions()->withTimestamps()->detach(Permission::getPermissions(stringToArray($permissions), $this->owned_by));

		return $this;
	}

	public function assignParentRole($roles) {
		$roles = stringToArray($roles);
		$toAdd = static::getRoles($roles, $this->owned_by);

		if (count($toAdd) !== count($roles))
			throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);

		if ($toAdd->find($this->id))
			throw new PortailException('Il n\'est pas possible de s\'auto-hériter', 400);

		if ($toAdd->whereIn('id', $this->children()->get(['id'])->pluck('id'))->count() > 0)
			throw new PortailException('Il n\'est pas possible d\'hériter de ses enfants', 400);

		$this->parents()->withTimestamps()->attach($toAdd);

		return $this;
	}

	public function removeParentRole($roles) {
		$roles = stringToArray($roles);
		$toAdd = static::getRoles($roles, $this->owned_by);

		if (count($toAdd) !== count($roles))
			throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);

		$this->parents()->withTimestamps()->detach($roles);

		return $this;
	}

	public function syncParentRole($roles) {
		$roles = stringToArray($roles);
		$toAdd = static::getRoles($roles, $this->owned_by);

		if (count($toAdd) !== count($roles))
			throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);

		if ($toAdd->find($this->id))
			throw new PortailException('Il n\'est pas possible de s\'auto-hériter', 400);

		if ($toAdd->whereIn('id', $this->children()->get(['id'])->pluck('id'))->count() > 0)
			throw new PortailException('Il n\'est pas possible d\'hériter de ses enfants', 400);

		$this->parents()->withTimestamps()->sync($roles);

		return $this;
	}

	public static function getTopStage(array $data = [], $with = []) {
        $tableName = (new static)->getTable();
        $model = static::doesntHave('parents')->with($with);

		foreach ($data as $key => $value) {
            if (!\Schema::hasColumn($tableName, $key))
                throw new PortailException('L\'attribut '.$key.' n\'existe pas');

            $model = $model->where($key, $value);
        }

		return $model->get();
    }

	public function isDeletable() {
		// On ne permet la suppression de rôles parents
		if ($this->children()->count() > 0)
			return false;

		if ($id)
			return resolve($this->owned_by_type)->isRoleForIdDeletable($this, $id);
		else
			return resolve($this->owned_by_type)->isRoleDeletable($this);
	}

	function __call($method, $arguments) {
		if (class_exists($this->owned_by_type) && method_exists($this->owned_by, 'getRoleRelationTable'))
			return $this->belongsToMany($this->owned_by_type, $this->owned_by->getRoleRelationTable());

		return parent::__call($method, $arguments);
    }
}

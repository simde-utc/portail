<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Model\HasStages;
use App\Traits\Model\HasPermissions;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Exceptions\PortailException;

class Role extends Model
{
	use HasStages, HasPermissions;

	protected $casts = [
		'limited_at' => 'integer',
	];

	/**
	 * Méthode appelée au chargement du trait
	 */
    public static function boot() {
        static::deleting(function ($model) {
			return resolve('\\App\\Models\\'.studly_case(str_singular(explode('-', $model->only_for)[0])))->beforeDeletingRole($model);
        });
    }

	public static function find(int $id, string $only_for = null) {
		$roles = static::where('id', $id);

		if ($only_for !== null) {
			$group = explode('-', $only_for)[0] ?? $only_for;

			$roles->where(function ($query) use ($group, $only_for) {
				$query->where('only_for', $group)->orWhere('only_for', $only_for);
			});
		}

		return $roles->first();
	}

	public static function findByType(string $type, string $only_for = null) {
		$roles = static::where('type', $type);

		if ($only_for !== null) {
			$group = explode('-', $only_for)[0] ?? $only_for;

			$roles->where(function ($query) use ($group, $only_for) {
				$query->where('only_for', $group)->orWhere('only_for', $only_for);
			});
		}

		return $roles->first();
	}

	public static function getRole($role, string $only_for = null) {
    	if (is_string($role))
    		return static::findByType($role, $only_for);
    	else if (is_int($role))
			return static::find($role, $only_for);
		else
			return $role;
	}

	public static function getRoles($roles, string $only_for = null) {
		$group = explode('-', $only_for)[0] ?? $only_for;

		if (is_array($roles)) {
			$query = static::where(function ($query) use ($roles) {
				$query->whereIn('id', $roles)->orWhereIn('type', $roles);
			});

			if ($only_for) {
				$query = $query->where(function ($query) use ($group, $only_for) {
					$query->where('only_for', $group)->orWhere('only_for', $only_for);
				});
			}

			return $query->get();
		}
		else if ($roles instanceof \Illuminate\Database\Eloquent\Model)
			return collect($roles);
		else
			return $roles;
	}

	public static function getRoleAndItsParents($role, string $only_for = null) {
		$role = static::getRole($role, $only_for);

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
		$this->permissions()->withTimestamps()->attach(Permission::getPermissions(stringToArray($permissions), $this->only_for));

		return $this;
	}

	public function removePermissionTo($permissions) {
		$this->permissions()->withTimestamps()->detach(Permission::getPermissions(stringToArray($permissions), $this->only_for));

		return $this;
	}

	public function assignParentRole($roles) {
		$roles = stringToArray($roles);
		$toAdd = static::getRoles($roles, $this->only_for);

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
		$toAdd = static::getRoles($roles, $this->only_for);

		if (count($toAdd) !== count($roles))
			throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);

		$this->parents()->withTimestamps()->detach($roles);

		return $this;
	}

	public function syncParentRole($roles) {
		$roles = stringToArray($roles);
		$toAdd = static::getRoles($roles, $this->only_for);

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

		@list($tableName, $id) = explode('-', $this->only_for);

		if ($id)
			return resolve('\\App\\Models\\'.studly_case(str_singular($tableName)))->isRoleForIdDeletable($this, $id);
		else
			return resolve('\\App\\Models\\'.studly_case(str_singular($tableName)))->isRoleDeletable($this);
	}

	function __call($method, $arguments) {
		$class = '\\App\\Models\\'.studly_case(str_singular(explode('-', $method)[0]));

		if (class_exists($class) && method_exists($class, 'getRoleRelationTable'))
			return $this->belongsToMany($class, resolve($class)->getRoleRelationTable());

		return parent::__call($method, $arguments);
    }
}

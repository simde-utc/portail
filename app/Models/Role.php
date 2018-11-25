<?php
/**
 * Modèle correspondant aux rôles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasStages;
use App\Traits\Model\HasPermissions;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Exceptions\PortailException;
use App\Interfaces\Model\CanHaveRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

class Role extends Model implements OwnableContract
{
    use HasMorphOwner;

    protected $fillable = [
        'type', 'name', 'description', 'limited_at', 'owned_by_id', 'owned_by_type',
    ];

    protected $hidden = [
        'owned_by_id', 'owned_by_type',
    ];

    protected $with = [
        'owned_by',
    ];

    protected $withModelName = [
        'owned_by',
    ];

    protected $casts = [
        'limited_at' => 'integer',
    ];

    protected $must = [
        'type', 'name', 'description', 'owned_by',
    ];

    protected $selection = [
        'owner' => [],
    ];

    /**
     * Appelé au lancement du modèle.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            return $model->owned_by->beforeDeletingRole($model);
        });
    }

    /**
     * Scope pour obtenir les rôles liés à un owner.
     *
     * @param  Builder $query
     * @param  string  $owner_type
     * @param  string  $owner_id
     * @return Builder
     */
    public function scopeOwner(Builder $query, string $owner_type, string $owner_id=null)
    {
        $query = $query->where('owned_by_type', \ModelResolver::getModelName($owner_type));

        if ($owner_id) {
            $query->where(function ($query) use ($owner_id) {
                return $query->whereNull('owned_by_id')
                	->orWhere('owned_by_id', $owner_id);
            });
        }

        return $query;
    }

    /**
     * Récupère un rôle.
     *
     * @param  string       $role_id
     * @param  CanHaveRoles $owner
     * @return Role|null
     */
    public static function find(string $role_id, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $roles = static::where('id', $role_id)
	        ->where('owned_by_type', get_class($owner))
	        ->where(function ($query) use ($owner) {
	            $query->whereNull('owned_by_id')
	            	->orWhere('owned_by_id', $owner->id);
	        });

        return $roles->first();
    }

    /**
     * Retrouve un rôle par son type.
     * TODO: Transformer en scope.
     *
     * @param  string       $type
     * @param  CanHaveRoles $owner
     * @return Role|null
     */
    public static function findByType(string $type, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $roles = static::where('type', $type)
	        ->where('owned_by_type', get_class($owner))
	        ->where(function ($query) use ($owner) {
	            $query->whereNull('owned_by_id')
	            	->orWhere('owned_by_id', $owner->id);
	        });

        return $roles->first();
    }

    /**
     * Retrouve une role via une donnée qui lui correspond.
     *
     * @param  mixed        $role
     * @param  CanHaveRoles $owner
     * @return Role|null
     */
    public static function getRole($role, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        if (Uuid::isValid($role)) {
            return static::find($role, $owner);
        } else if (is_string($role)) {
            return static::findByType($role, $owner);
        } else {
            return $role;
        }
    }

    /**
     * Retrouve plusieurs roles via une donnée qui leur correspond.
     *
     * @param  mixed        $roles
     * @param  CanHaveRoles $owner
     * @return Collection
     */
    public static function getRoles($roles, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        if (is_array($roles)) {
            $roles = static::where(function ($query) use ($roles) {
                $query->whereIn('id', $roles)->orWhereIn('type', $roles);
            })->where('owned_by_type', get_class($owner))
            ->where(function ($query) use ($owner) {
                  $query->whereNull('owned_by_id')
                   ->orWhere('owned_by_id', $owner->id);
            });

            return $roles->get();
        } else if ($roles instanceof Model) {
            return collect($roles);
        } else {
            return $roles;
        }
    }

    /**
     * Donne le rôle et ses parents.
     *
     * @param  mixed        $role
     * @param  CanHaveRoles $owner
     * @return Collection|null
     */
    public static function getRoleAndItsParents($role, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $role = static::getRole($role, $owner);

        if ($role === null) {
            return null;
        }

        $roles = $role->parents;
        $roles->push($role);

        return $roles;
    }

    /**
     * Surcharge de la méthode pour obtenir un array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if (($array['owned_by'] ?? null) === null) {
            $array['owned_by'] = [
                'model' => \ModelResolver::getName($this->owned_by),
            ];
        } else {
            $array['owned_by'] = $this->owned_by->hideData(true);
        }

        return $array;
    }

    /**
     * Relation avec les permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    /**
     * Relation avec les rôles enfants.
     *
     * @return BelongsToMany
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_parents', 'parent_id', 'role_id');
    }

    /**
     * Relation avec les rôles parents.
     *
     * @return BelongsToMany
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_parents', 'role_id', 'parent_id');
    }

    /**
     * Modifie à la volée l'attribut owned_by.
     *
     * @return mixed
     */
    public function getOwnedByAttribute()
    {
        return ($this->owned_by()->first() ?? resolve($this->owned_by_type));
    }

    /**
     * Relation avec le possédeur.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo('owned_by');
    }

    /**
     * Récupère tous les enfants hérités.
     *
     * @return Collection
     */
    public function allChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);

            $children = $children->merge($child->allChildren());
            $child->makeHidden('children');
        }

        return $children->unique('id');
    }

    /**
     * Récupère tous les parents hérités.
     *
     * @return Collection
     */
    public function allParents()
    {
        $parents = collect();

        foreach ($this->parents as $parent) {
            $parents->push($parent);

            $parents = $parents->merge($parent->allChildren());
            $parent->makeHidden('parents');
        }

        return $parents->unique('id');
    }

    /**
     * Indique si un rôle possède une permission.
     *
     * @param  mixed $permission
     * @return boolean
     */
    public function hasPermissionTo($permission): bool
    {
        if (Uuid::isValid($permission)) {
            $permission = Permission::find($permission);
        } else if (is_string($permission)) {
            $permission = Permission::findByType($permission);
        }

        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * Donne au rôle une permission.
     *
     * @param  mixed $permissions
     * @return Role
     */
    public function givePermissionTo($permissions)
    {
        $this->permissions()->withTimestamps()->attach(
        Permission::getPermissions(stringToArray($permissions), $this->owned_by)
        );

        return $this;
    }

    /**
     * Retire au rôle une permission.
     *
     * @param  mixed $permissions
     * @return Role
     */
    public function removePermissionTo($permissions)
    {
        $this->permissions()->withTimestamps()->detach(
        Permission::getPermissions(stringToArray($permissions), $this->owned_by)
        );

        return $this;
    }

    /**
     * Assigne des rôles parents.
     *
     * @param  mixed $roles
     * @return Role
     */
    public function assignParentRole($roles)
    {
        $roles = stringToArray($roles);
        $toAdd = static::getRoles($roles, $this->owned_by);

        if (count($toAdd) !== count($roles)) {
            throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);
        }

        $thisRole = $toAdd->filter(function ($role) {
            return $role->id === $this->id;
        });

        if ($thisRole->count() > 0) {
            throw new PortailException('Il n\'est pas possible de s\'auto-hériter', 400);
        }

        if ($toAdd->whereIn('id', $this->children()->get(['id'])->pluck('id'))->count() > 0) {
            throw new PortailException('Il n\'est pas possible d\'hériter de ses enfants', 400);
        }

        $this->parents()->withTimestamps()->attach($toAdd);

        return $this;
    }

    /**
     * Retire des rôles parents.
     *
     * @param  mixed $roles
     * @return Role
     */
    public function removeParentRole($roles)
    {
        $roles = stringToArray($roles);
        $toAdd = static::getRoles($roles, $this->owned_by);

        if (count($toAdd) !== count($roles)) {
            throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);
        }

        $this->parents()->withTimestamps()->detach($roles);

        return $this;
    }

    /**
     * Met à jour les rôles parents.
     *
     * @param  mixed $roles
     * @return Role
     */
    public function syncParentRole($roles)
    {
        $roles = stringToArray($roles);
        $toAdd = static::getRoles($roles, $this->owned_by);

        if (count($toAdd) !== count($roles)) {
            throw new PortailException('Les rôles donnés n\'existent pas ou ne sont pas associés au même type', 400);
        }

        $thisRole = $toAdd->filter(function ($role) {
            return $role->id === $this->id;
        });

        if ($thisRole->count() > 0) {
            throw new PortailException('Il n\'est pas possible de s\'auto-hériter', 400);
        }

        if ($toAdd->whereIn('id', $this->children()->get(['id'])->pluck('id'))->count() > 0) {
            throw new PortailException('Il n\'est pas possible d\'hériter de ses enfants', 400);
        }

        $this->parents()->withTimestamps()->sync($roles);

        return $this;
    }

    /**
     * Indique si un rôle est supprimable.
     *
     * @return boolean
     */
    public function isDeletable()
    {
        // On ne permet la suppression de rôles parents.
        if ($this->children()->count() > 0) {
            return false;
        }

        if ($this->owned_by_id) {
            return resolve($this->owned_by_type)->isRoleForIdDeletable($this, $this->owned_by_id);
        } else {
            return resolve($this->owned_by_type)->isRoleDeletable($this);
        }
    }

    /**
     * Surcharge de la méthode d'appel pour retourner une relation.
     *
     * @param  mixed $method
     * @param  mixed $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (class_exists($this->owned_by_type) && method_exists($this->owned_by, 'getRoleRelationTable')) {
            return $this->belongsToMany($this->owned_by_type, $this->owned_by->getRoleRelationTable());
        }

        return parent::__call($method, $arguments);
    }
}

<?php
/**
 * Modèle correspondant aux permissions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasOwnerSelection;
use Illuminate\Support\Collection;
use App\Interfaces\Model\CanHavePermissions;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Uuid;

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

    protected $selection = [
        'owner' => [],
    ];

    /**
     * Relation avec les rôles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    /**
     * Relation avec les utilisateurs.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_permissions');
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
                'model' => \ModelResolver::getNameFromObject($this->owned_by),
            ];
        } else {
            $array['owned_by'] = $this->owned_by->hideData(true);
        }

        return $array;
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
     * Scope pour obtenir les permissions liés à un owner.
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
     * Retrouve une permission.
     *
     * @param  string             $permission_id
     * @param  CanHavePermissions $owner
     * @return Permission
     */
    public static function find(string $permission_id, CanHavePermissions $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $permissions = static::where('id', $permission_id)
	        ->where('owned_by_type', get_class($owner))
	        ->where(function ($query) use ($owner) {
	            $query->whereNull('owned_by_id')
	               ->orWhere('owned_by_id', $owner->id);
	        });

        return $permissions->first();
    }

    /**
     * Retrouve une permission par son type.
     * TODO: Transformer en scope.
     *
     * @param  string             $type
     * @param  CanHavePermissions $owner
     * @return Permission
     */
    public static function findByType(string $type, CanHavePermissions $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $permissions = static::where('type', $type)
            ->where('owned_by_type', get_class($owner))
            ->where(function ($query) use ($owner) {
                $query->whereNull('owned_by_id')
                    ->orWhere('owned_by_id', $owner->id);
            });

        return $permissions->first();
    }

    /**
     * Retrouve une permission via une donnée qui lui correspond.
     *
     * @param  mixed              $permission
     * @param  CanHavePermissions $owner
     * @return Permission
     */
    public static function getPermission($permission, CanHavePermissions $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        if (Uuid::isValid($permission)) {
            return static::find($permission, $owner);
        } else if (is_string($permission)) {
            return static::findByType($permission, $owner);
        } else {
            return $permission;
        }
    }

    /**
     * Retrouve plusieurs permissions via une donnée qui leur correspond.
     *
     * @param  mixed              $permissions
     * @param  CanHavePermissions $owner
     * @return Collection
     */
    public static function getPermissions($permissions, CanHavePermissions $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        if (is_array($permissions)) {
            $permissions = static::where(function ($query) use ($permissions) {
                $query->whereIn('id', $permissions)->orWhereIn('type', $permissions);
            })->where('owned_by_type', get_class($owner))
            ->where(function ($query) use ($owner) {
                  $query->whereNull('owned_by_id')
                   ->orWhere('owned_by_id', $owner->id);
            });

            return $permissions->get();
        } else if ($permissions instanceof Model) {
            return collect($permissions);
        } else {
            return $permissions;
        }
    }
}

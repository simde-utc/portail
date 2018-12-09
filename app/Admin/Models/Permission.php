<?php
/**
 * Modèle correspondant aux admins.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Permission as BasePermission;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Permission extends BasePermission
{
    protected $table = 'permissions';

    protected $fillable = [
        'type', 'name', 'description', 'owned_by_id', 'owned_by_type',
    ];

    protected $hidden = [
        'owned_by_id', 'owned_by_type',
    ];

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
	        ->where('owned_by_type', User::class)
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
            ->where('owned_by_type', User::class)
            ->where(function ($query) use ($owner) {
                $query->whereNull('owned_by_id')
                    ->orWhere('owned_by_id', $owner->id);
            });

        return $permissions->first();
    }

    public static function pluck(string ...$names)
    {
        if (($key = array_search('slug', $names)) !== false) {
            unset($names[$key]);
        }

        return static::getQuery()->pluck(...$names);
    }
}

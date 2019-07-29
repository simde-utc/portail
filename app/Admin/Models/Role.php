<?php
/**
 * Admin roles model.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Role as BaseRole;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Interfaces\Model\CanHaveRoles;

class Role extends BaseRole
{
    protected $table = 'roles';

    protected $fillable = [
        'type', 'name', 'description', 'owned_by_id', 'owned_by_type',
    ];

    protected $hidden = [
        'owned_by_id', 'owned_by_type',
    ];

    /**
     * Get the relation with owner.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo('owned_by');
    }

    /**
     * Find a role.
     *
     * @param  string       $role_id
     * @param  CanHaveRoles $owner
     * @return Role
     */
    public static function find(string $role_id, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $roles = static::where('id', $role_id)
	        ->where('owned_by_type', User::class)
	        ->where(function ($query) use ($owner) {
	            $query->whereNull('owned_by_id')
	               ->orWhere('owned_by_id', $owner->id);
	        });

        return $roles->first();
    }

    /**
     * Find a fole by type.
     * TODO: Transform it into a scope.
     *
     * @param  string       $type
     * @param  CanHaveRoles $owner
     * @return Role
     */
    public static function findByType(string $type, CanHaveRoles $owner=null)
    {
        if ($owner === null) {
            $owner = new User;
        }

        $roles = static::where('type', $type)
            ->where('owned_by_type', User::class)
            ->where(function ($query) use ($owner) {
                $query->whereNull('owned_by_id')
                    ->orWhere('owned_by_id', $owner->id);
            });

        return $roles->first();
    }
}

<?php
/**
 * Add admin permissions management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Encore\Admin\Auth\Database\HasPermissions;

trait HasAdminPermissions
{
    use HasPermissions;

    /**
     * Get all permissions of user.
     *
     * @return mixed
     */
    public function allPermissions() : Collection
    {
        return $this->getUserPermissions();
    }

    /**
     * Check if user has permission.
     *
     * @param string $permission
     * @return boolean
     */
    public function can(string $permission) : bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->hasOnePermission($permission);
    }

    /**
     * Check if user is administrator.
     *
     * @return mixed
     */
    public function isAdministrator() : bool
    {
        return $this->hasOneRole('admin');
    }

    /**
     * Check if user is $role.
     *
     * @param string $role
     * @return mixed
     */
    public function isRole(string $role) : bool
    {
        return $this->hasOneRole($role);
    }

    /**
     * Check if user in $roles.
     *
     * @param array $roles
     * @return mixed
     */
    public function inRoles(array $roles=[]) : bool
    {
        return $this->hasOneRole($roles);
    }

    /**
     * If visible for roles.
     *
     * @param array $roles
     * @return boolean
     */
    public function visible(array $roles=[]) : bool
    {
        if (empty($roles)) {
            return true;
        }

        return $this->inRoles($roles) || $this->isAdministrator();
    }
}

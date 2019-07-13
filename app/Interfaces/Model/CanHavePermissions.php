<?php
/**
 * Indicates that the model can have permissions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHavePermissions
{
    /**
     * Returns the permission list.
     *
     * @return MorphMany
     */
    public function permissions();

    /**
     * Indicates if a given user can access the model's permissions
     *
     * @param string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool;

    /**
     * Indicates if a given user can can create/update/delete the model's permissions
     *
     * @param string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool;
}

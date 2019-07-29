<?php
/**
 * Indicate that the model can have permissions.
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
     * Return the permission list.
     *
     * @return MorphMany
     */
    public function permissions();

    /**
     * Indicate if a given user can access the model's permissions
     *
     * @param string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool;

    /**
     * Indicate if a given user can can create/update/delete the model's permissions
     *
     * @param string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool;
}

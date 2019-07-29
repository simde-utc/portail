<?php
/**
 * Indicate that the model can have roles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveRoles
{
    /**
     * Return the roles list.
     *
     * @return MorphMany
     */
    public function roles();

    /**
     * Indicate  if a given user can access the model's roles.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool;

    /**
     *
     * Indicate  if a given user can can create/update/delete the model's roles.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool;
}

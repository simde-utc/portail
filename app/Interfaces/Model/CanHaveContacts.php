<?php
/**
 * Indicate that the model can have contacts.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveContacts
{
    /**
     * Return the contacts list.
     *
     * @return MorphMany
     */
    public function contacts();

    /**
     * Indicate if a given user can can create/update/delete the model's contacts.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isContactManageableBy(string $user_id): bool;
}

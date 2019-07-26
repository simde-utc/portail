<?php
/**
 * Indicate that the model can have events.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveEvents
{
    /**
     * Return the events list.
     *
     * @return MorphMany
     */
    public function events();

    /**
     * Indicate if a given user can can create/update/delete the model's events.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool;
}

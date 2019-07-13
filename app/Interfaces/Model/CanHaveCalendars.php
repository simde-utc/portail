<?php
/**
 * Indicates that the model can have calendars.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveCalendars
{
    /**
     * Returns the calendars list.
     *
     * @return MorphMany
     */
    public function calendars();

    /**
     * Indicates if a given user can can create/update/delete the model's calendars.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool;
}

<?php
/**
 * Indicates that the model can have bookings.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveBookings
{
    /**
     * Returns the bookings list.
     *
     * @return MorphMany
     */
    public function bookings();

    /**
     * Indicates if a given user can access the model's bookings.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isBookingAccessibleBy(string $user_id): bool;

    /**
     * Indicates if a given user can can create/update/delete the model's bookings.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isBookingManageableBy(string $user_id): bool;

    /**
     * Indicates if a given model can validate this model's bookings.
     *
     * @param Model $model
     * @return boolean
     */
    public function isBookingValidableBy(Model $model): bool;
}

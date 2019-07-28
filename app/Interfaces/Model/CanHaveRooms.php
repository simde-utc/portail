<?php
/**
 * Indicate that the model can have rooms.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

interface CanHaveRooms
{
    /**
     * Return the rooms list.
     *
     * @return MorphMany
     */
    public function rooms();

    /**
     * Indicate if the given user can create/update/delete this model's rooms.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isRoomManageableBy(string $user_id): bool;

    /**
     * Indicate if a model can book this model's rooms.
     *
     * @param Model $model
     * @return boolean
     */
    public function isRoomReservableBy(Model $model): bool;
}

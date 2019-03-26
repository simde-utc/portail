<?php
/**
 * Indique que le modèle peut posséder des salles.
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
     * Renvoie la liste des salles.
     *
     * @return MorphMany
     */
    public function rooms();

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les salles appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isRoomManageableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si un modèle à le droit de réserver les salles appartenant au modèle.
     *
     * @param Model $model
     * @return boolean
     */
    public function isRoomReservableBy(Model $model): bool;
}

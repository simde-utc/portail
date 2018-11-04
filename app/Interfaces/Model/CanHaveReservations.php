<?php
/**
 * Indique que le modèle peut posséder des réservations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Model;

interface CanHaveReservations
{
    /**
     * Renvoie la liste des réservations.
     *
     * @return MorphMany
     */
    public function reservations();

    /**
     * Permet d'indiquer si la personne à le droit de voir les réservations appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isReservationAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les réservations appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isReservationManageableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si le modèle à le droit de valider les réservations appartenant au modèle.
     *
     * @param Model $model
     * @return boolean
     */
    public function isReservationValidableBy(Model $model): bool;
}

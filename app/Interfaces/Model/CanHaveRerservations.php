<?php

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Model;

Interface CanHaveReservations {
    /**
     * Renvoie la liste des réservations
     * @return MorphMany
     */
    public function reservations();
    /**
     * Permet d'indiquer si la personne à le droit de voir les réservations appartenant au modèle
     * @return boolean
     */
    public function isReservationAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les réservations appartenant au modèle
     * @return boolean
     */
    public function isReservationManageableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si le modèle à le droit de valider les réservations appartenant au modèle
     * @return boolean
     */
    public function isReservationValidableBy(Model $model): bool;
}

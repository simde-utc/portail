<?php

namespace App\Interfaces\Model;

use Illuminate\Database\Eloquent\Model;

Interface CanHaveRooms {
    /**
     * Renvoie la liste des salles
     * @return MorphMany
     */
    public function rooms();
    /**
     * Permet d'indiquer si la personne à le droit de voir les salles appartenant au modèle
     * @return boolean
     */
    public function isRoomAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les salles appartenant au modèle
     * @return boolean
     */
    public function isRoomManageableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si un modèle à le droit de réserver les salles appartenant au modèle
     * @return boolean
     */
    public function isRoomReservableBy(Model $model): bool;
}

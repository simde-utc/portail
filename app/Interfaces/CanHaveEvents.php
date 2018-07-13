<?php

namespace App\Interfaces;

Interface CanHaveEvents {
    /**
     * Renvoie la liste des évènements
     * @return MorphMany
     */
    public function events();
    /**
     * Permet d'indiquer si la personne à le droit de voir les évènements appartenant au modèle
     * @return boolean
     */
    public function isEventAccessibleBy(int $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les évènements appartenant au modèle
     * @return boolean
     */
    public function isEventManageableBy(int $user_id): bool;
}

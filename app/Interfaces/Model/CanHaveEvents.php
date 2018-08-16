<?php

namespace App\Interfaces\Model;

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
    public function isEventAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les évènements appartenant au modèle
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool;
}

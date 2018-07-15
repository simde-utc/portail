<?php

namespace App\Interfaces;

Interface CanHaveContacts {
    /**
     * Renvoie la liste des contacts
     * @return MorphMany
     */
    public function contacts();

    /**
     * Permet d'indiquer si la personne à le droit de voir les contacts appartenant au modèle
     * @return boolean
     */
    public function isContactAccessibleBy(int $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les contacts appartenant au modèle
     * @return boolean
     */
    public function isContactManageableBy(int $user_id): bool;
}

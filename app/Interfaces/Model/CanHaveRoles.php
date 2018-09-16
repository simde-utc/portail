<?php

namespace App\Interfaces\Model;

Interface CanHaveRoles {
    /**
     * Renvoie la liste des roles
     * @return MorphMany
     */
    public function roles();

    /**
     * Permet d'indiquer si la personne à le droit de voir les rôles appartenant au modèle
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les rôles appartenant au modèle
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool;
}

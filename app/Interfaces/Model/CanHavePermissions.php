<?php

namespace App\Interfaces\Model;

Interface CanHavePermissions {
    /**
     * Renvoie la liste des roles
     * @return MorphMany
     */
    public function permissions();

      /**
       * Permet d'indiquer si la personne à le droit de voir les permissions appartenant au modèle
       * @return boolean
       */
      public function isPermissionAccessibleBy(string $user_id): bool;

      /**
       * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les permissions appartenant au modèle
       * @return boolean
       */
      public function isPermissionManageableBy(string $user_id): bool;
}

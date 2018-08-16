<?php

namespace App\Interfaces\Model;

Interface CanHaveArticles {
    /**
     * Renvoie la liste des articles
     * @return MorphMany
     */
    public function articles();

    /**
     * Permet d'indiquer si la personne à le droit de voir les articles appartenant au modèle
     * @return boolean
     */
    public function isArticleAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les articles appartenant au modèle
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool;
}

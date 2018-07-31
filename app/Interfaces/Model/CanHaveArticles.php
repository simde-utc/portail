<?php

namespace App\Interfaces\Model;

Interface CanHaveArticles {
    /**
     * Renvoie la liste des articles
     * @return MorphMany
     */
    public function articles();

    /**
     * Renvoie la liste des articles où on a collaboré
     * @return MorphMany
     */
    public function collaboratedArticles();

    /**
     * Permet d'indiquer si la personne à le droit de voir les articles appartenant au modèle
     * @return boolean
     */
    public function isArticleAccessibleBy(int $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les articles appartenant au modèle
     * @return boolean
     */
    public function isArticleManageableBy(int $user_id): bool;
}

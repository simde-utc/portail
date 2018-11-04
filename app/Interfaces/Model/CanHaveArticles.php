<?php
/**
 * Indique que le modèle peut posséder des articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

interface CanHaveArticles
{
    /**
     * Renvoie la liste des articles.
     *
     * @return MorphMany
     */
    public function articles();

    /**
     * Permet d'indiquer si la personne à le droit de voir les articles appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isArticleAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de créer/modifier/supprimer les articles appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool;
}

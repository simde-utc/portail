<?php
/**
 * Indique que le modèle peut posséder des commentaires.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

use App\Interfaces\Model\CanComment;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CanHaveComments
{
    /**
     * Renvoie la liste des commentaires.
     *
     * @return MorphMany
     */
    public function comments();

    /**
     * Permet d'indiquer si la personne à le droit de voir les commentaires appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si l'instance à le droit de commenter/gérer ses commentaires.
     *
     * @param CanComment $model
     * @return boolean
     */
    public function isCommentManageableBy(CanComment $model): bool;
}

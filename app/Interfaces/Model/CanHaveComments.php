<?php

namespace App\Interfaces\Model;

use App\Interfaces\Model\CanComment;

Interface CanHaveComments {
    /**
     * Renvoie la liste des commentaires
     * @return MorphMany
     */
    public function comments();

    /**
     * Permet d'indiquer si la personne à le droit de voir les commentaires appartenant au modèle
     * @return boolean
     */
    public function isCommentAccessibleBy(string $user_id): bool;

    /**
     * Permet d'indiquer si l'instance à le droit de commenter/gérer ses commentaires
     * @return boolean
     */
    public function isCommentManageableBy(CanComment $model): bool;
}

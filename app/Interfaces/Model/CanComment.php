<?php

namespace App\Interfaces\Model;

// Défini les modèles pouvant commenter
Interface CanComment {
    /**
     * Permet d'indiquer si la personne à le droit de commenter pour ce modèle
     * @return boolean
     */
    public function isCommentWritableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de modifier les commentaires appartenant au modèle
     * @return boolean
     */
    public function isCommentEditableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de supprimer les commentaires appartenant au modèle
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool;
}

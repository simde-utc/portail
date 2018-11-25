<?php
/**
 * Indique que le modèle peut commenter.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

interface CanComment
{
    /**
     * Permet d'indiquer si la personne à le droit de commenter pour ce modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentWritableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de modifier les commentaires appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentEditableBy(string $user_id): bool;

    /**
     * Permet d'indiquer si la personne à le droit de supprimer les commentaires appartenant au modèle.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool;
}

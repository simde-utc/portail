<?php
/**
 * Indicates that the model can commnent.
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
     * Indicates if a user can comment for this model.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentWritableBy(string $user_id): bool;

    /**
     * Indicates if a user can modifiy comments for this model.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentEditableBy(string $user_id): bool;

    /**
     * Indicates if a user can delete comments for this model.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool;
}

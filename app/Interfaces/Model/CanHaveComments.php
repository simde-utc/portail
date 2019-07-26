<?php
/**
 * Indicate that the model can have comments.
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
     * Return the comment list.
     *
     * @return MorphMany
     */
    public function comments();

    /**
     * Indicate if a given user can access the model's comments..
     *
     * @param string $user_id
     * @return boolean
     */
    public function isCommentAccessibleBy(string $user_id): bool;

    /**
     * Indicate if the instance has the rights to comment/manage it's comments.
     *
     * @param CanComment $model
     * @return boolean
     */
    public function isCommentManageableBy(CanComment $model): bool;
}

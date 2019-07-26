<?php
/**
 * Indicate that the model can have articles.
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
     * Return the articles list.
     *
     * @return mixed
     */
    public function articles();

    /**
     * Indicate if a given user can can create/update/delete the model articles.
     *
     * @param string $user_id
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool;
}

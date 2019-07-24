<?php
/**
 * Manage ArticleActions as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\ArticleAction;
use App\Models\Article;
use App\Models\User;

class ArticleActionController extends ResourceController
{
    protected $model = ArticleAction::class;

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'article' => Article::get(['id', 'title'])->map(function ($article) {
                $article->name = $article->title;

                return $article;
            }),
            'user' => User::get(['id', 'firstname', 'lastname']),
            'key' => 'text',
            'value' => 'text',
            'type' => 'display',
            'created_at' => 'display',
            'updated_at' => 'display',
        ];
    }

    /**
     * Default values definition of the fields to display.
     *
     * @return array
     */
    protected function getDefaults(): array
    {
        return [
            'user_id' => ($user_id = \Auth::guard('admin')->user()->id),
        ];
    }

    /**
     * Return dependencies.
     *
     * @return array
     */
    protected function getWith(): array
    {
        return [
            'article', 'user'
        ];
    }
}

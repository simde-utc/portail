<?php
/**
 * Gère en admin les actions des articles.
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
     * Définition des champs à afficher.
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
     * Définition des valeurs par défaut champs à afficher.
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
     * Retourne les dépendances.
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

<?php
/**
 * Gère en admin les articles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Article;
use App\Models\Event;
use App\Models\Visibility;
use App\Models\User;

class ArticleController extends ResourceController
{
    protected $model = Article::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'title' => 'text',
            'description' => 'textarea',
            'content' => 'text',
            'image' => 'image',
            'event' => Event::get(['id', 'name']),
            'visibility' => Visibility::get(['id', 'name']),
            'tags' => 'display',
            'created_by' => 'display',
            'owned_by' => 'display',
            'created_at' => 'display',
            'updated_at' => 'display'
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
            'visibility_id' => Visibility::first()->id,
            'user_id' => ($user_id = \Auth::guard('admin')->user()->id),
            'created_by_type' => User::class,
            'created_by_id' => $user_id
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
            'created_by', 'owned_by', 'tags', 'visibility', 'event',
        ];
    }
}

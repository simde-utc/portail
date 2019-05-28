<?php
/**
 * Gère en admin les questions FAQs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    FaqCategory, Visibility
};

class FaqCategoryController extends ResourceController
{
    protected $model = FaqCategory::class;

    /**
     * Définition des champs à afficher.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'name' => 'text',
            'description' => 'textarea',
            'lang' => 'text',
            'parent' => FaqCategory::get(['id', 'name']),
            'visibility' => Visibility::get(['id', 'name']),
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
        $category = FaqCategory::orderBy('created_at', 'DESC')->first();

        return [
            'lang' => 'fr',
            'parent_id' => is_null($category) ? null : $category->id,
            'visibility_id' => Visibility::first()->id,
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
            'parent', 'visibility'
        ];
    }
}

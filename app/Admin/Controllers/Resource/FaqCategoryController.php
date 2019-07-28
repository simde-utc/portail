<?php
/**
 * Manage FAQ questions categories as admin.
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
     * Field to display definition.
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
     * Default values definition of the fields to display.
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
     * Return dependencies.
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

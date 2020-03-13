<?php
/**
 * Manage FAQ questions as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Faq, FaqCategory, Visibility
};

class FaqController extends ResourceController
{
    protected $model = Faq::class;

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'question' => 'text',
            'answer' => 'textarea',
            'category' => FaqCategory::get(['id', 'name']),
            'visibility' => Visibility::get(['id', 'name']),
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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
            'category_id' => FaqCategory::orderBy('created_at', 'DESC')->first()->id,
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
            'category', 'visibility'
        ];
    }
}

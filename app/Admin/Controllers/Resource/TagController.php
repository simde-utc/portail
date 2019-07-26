<?php
/**
 * Manage Tags as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Tag;

class TagController extends ResourceController
{
    protected $model = Tag::class;

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'name' => 'text',
            'description' => 'textarea',
            'created_at' => 'display',
            'updated_at' => 'display'
        ];
    }
}

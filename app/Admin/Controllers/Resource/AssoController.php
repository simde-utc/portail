<?php
/**
 * Manages Associations as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Asso;
use App\Models\AssoType;

class AssoController extends ResourceController
{
    protected $model = Asso::class;

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
            'shortname' => 'text',
            'login' => 'text',
            'image' => 'image',
            'description' => 'textarea',
            'type' => AssoType::get(['id', 'name']),
            'parent' => Asso::get(['id', 'name']),
            'created_at' => 'display',
            'updated_at' => 'display',
            'deleted_at' => 'date',
        ];
    }

    /**
     * Returns dependencies.
     *
     * @return array
     */
    protected function getWith(): array
    {
        return [
            'type', 'parent'
        ];
    }
}

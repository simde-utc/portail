<?php
/**
 * Manages Places as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\Place;

class PlaceController extends ResourceController
{
    protected $model = Place::class;

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
            'address' => 'text',
            'city' => 'text',
            'country' => 'text',
            'position' => 'text',
            'created_at' => 'display',
            'updated_at' => 'display',
        ];
    }
}

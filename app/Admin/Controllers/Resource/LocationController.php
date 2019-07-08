<?php
/**
 * Manages Locations as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Location, Place
};

class LocationController extends ResourceController
{
    protected $model = Location::class;

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
            'place' => Place::get(['id', 'name']),
            'position' => 'text',
            'created_at' => 'display',
            'updated_at' => 'display',
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
            'place'
        ];
    }
}

<?php
/**
 * Manage Events as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Event, Location, Visibility, User
};

class EventController extends ResourceController
{
    protected $model = Event::class;

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
            'location' => Location::get(['id', 'name']),
            'visibility' => Visibility::get(['id', 'name']),
            'begin_at' => 'datetime',
            'end_at' => 'datetime',
            'full_day' => 'switch',
            'created_by' => 'display',
            'owned_by' => 'display',
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
            'visibility_id' => Visibility::first()->id,
            'created_by_type' => User::class,
            'created_by_id' => \Auth::guard('admin')->user()->id
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
            'location', 'visibility', 'created_by', 'owned_by'
        ];
    }
}

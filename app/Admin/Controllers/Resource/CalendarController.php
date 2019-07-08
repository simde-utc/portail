<?php
/**
 * Manages Calendars as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Calendar, User, Visibility
};

class CalendarController extends ResourceController
{
    protected $model = Calendar::class;

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
            'color' => 'color',
            'visibility' => Visibility::get(['id', 'name']),
            'created_by' => 'display',
            'owned_by' => 'display',
            'created_at' => 'display',
            'updated_at' => 'display'
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
     * Returns dependencies.
     *
     * @return array
     */
    protected function getWith(): array
    {
        return [
            'visibility', 'created_by', 'owned_by'
        ];
    }
}

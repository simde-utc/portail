<?php
/**
 * Manages EventDetails as admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers\Resource;

use App\Models\{
    Event, EventDetail
};

class EventDetailController extends ResourceController
{
    protected $model = EventDetail::class;

    /**
     * Fields to display definition.
     *
     * @return array
     */
    protected function getFields(): array
    {
        return [
            'id' => 'display',
            'event' => Event::get(['id', 'name']),
            'key' => 'text',
            'value' => 'text',
            'type' => 'display',
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
            'event'
        ];
    }
}

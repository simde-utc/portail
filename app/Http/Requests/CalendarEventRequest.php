<?php
/**
 * Calendar events request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class CalendarEventRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event_ids' => Validation::type('array')
                ->get(),
            'event_id' => Validation::type('uuid')
                ->exists('events', 'id')
                ->get(),
        ];
    }
}

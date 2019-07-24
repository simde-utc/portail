<?php
/**
 * User calendar request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserCalendarRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'calendar_ids' => Validation::type('array')
                ->get(),
            'calendar_id' => Validation::type('uuid')
                ->exists('calendars', 'id')
                ->get(),
        ];
    }
}

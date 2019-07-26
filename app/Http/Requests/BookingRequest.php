<?php
/**
 * Booking request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class BookingRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => Validation::type('string')
                ->length('title')
                ->unique('roles', 'name')
                ->post('required')
                ->get(),
            'begin_at' => Validation::type('date')
                ->post('required')
                ->get(),
            'end_at' => Validation::type('date')
                ->post('required')
                ->get(),
            'full_day' => Validation::type('boolean')
                ->get(),
            'type_id' => Validation::type('uuid')
                ->exists('rooms_bookings_types', 'id')
                ->post('required')
                ->get(),
            'created_by_type' => Validation::type('string')
                ->get(),
            'created_by_id' => Validation::type('uuid')
                ->get(),
            'owned_by_type' => Validation::type('string')
                ->post('required')
                ->get(),
            'owned_by_id' => Validation::type('uuid')
                ->post('required')
                ->get(),
            'validated_by_type' => Validation::type('string')
                ->get(),
            'validated_by_id' => Validation::type('uuid')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->get(),
        ];
    }
}

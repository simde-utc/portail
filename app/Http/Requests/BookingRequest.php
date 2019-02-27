<?php
/**
 * Gestion de la requête pour les réservations.
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
     * Défini les règles de validation des champs.
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
            'begin_at' => Validation::type('datetime')
                ->post('required')
                ->get(),
            'end_at' => Validation::type('datetime')
                ->post('required')
                ->get(),
            'full_day' => Validation::type('boolean')
                ->get(),
            'type_id' => Validation::type('uuid')
                ->exists('bookings_types', 'id')
                ->post('required')
                ->get(),
            'location_id' => Validation::type('uuid')
                ->exists('locations', 'id')
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
                ->post('required')
                ->get(),
            'validated_by_id' => Validation::type('uuid')
                ->post('required')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->get(),
        ];
    }
}

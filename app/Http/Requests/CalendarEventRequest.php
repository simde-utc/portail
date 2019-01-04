<?php
/**
 * Gestion de la requête pour les événements des calendriers.
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
     * Défini les règles de validation des champs.
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

<?php
/**
 * Gestion de la requête pour les visibilités.
 *
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use App\Facades\Validation;

class VisibilityRequest extends Request
{
    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => Validation::type('string')
                ->length('name')
                ->unique('visibilities', 'type')
                ->post('required')
                ->get(),
            'name' => Validation::type('string')
                ->length('name')
                ->unique('visibilities', 'name')
                ->post('required')
                ->get(),
            'parent_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->get(),
        ];
    }
}

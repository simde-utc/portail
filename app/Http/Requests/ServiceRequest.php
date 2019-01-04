<?php
/**
 * Gestion de la requête pour les services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class ServiceRequest extends Request
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
                ->unique('services', 'name')
                ->post('required')
                ->get(),
            'shortname' => Validation::type('string')
                ->length('title')
                ->unique('services', 'ivb  çol')
                ->post('required')
                ->get(),
            'login' => Validation::type('string')
                ->length('title')
                ->unique('services', 'login')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->get(),
            'url' => Validation::type('string')
                ->length('url')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->get(),
        ];
    }
}

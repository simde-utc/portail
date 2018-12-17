<?php
/**
 * Gestion de la requête pour les partenaires.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class PartnerRequest extends Request
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
                ->length('name')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->post('required')
                ->get(),
            'image' => Validation::type('image')
                ->length('url')
                ->post('required')
                ->get(),
        ];
    }
}

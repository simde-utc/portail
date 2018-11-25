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

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur à le droit de faire cette requête.
     * Tout est réalisé dans les controlleurs.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => Validation::make($this)
                ->type('string')
                ->length(validation_between('name'))
                ->post('required')
                ->get(),
            'description' => Validation::make($this)
                ->type('string')
                ->length(validation_between('description'))
                ->post('required')
                ->get(),
            'image' => Validation::make($this)
                ->type('image')
                ->length(validation_between('url'))
                ->post('required')
                ->get(),
        ];
    }
}

<?php
/**
 * Gestion de la requête pour les associations.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;

class AssoRequest extends FormRequest
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
        ->unique('assos', 'name')
        ->post('required')
        ->get(),
            'shortname' => Validation::make($this)
        ->type('string')
        ->length(validation_between('login'))
        ->unique('assos', 'shortname')
        ->post('required')
        ->get(),
            'login' => Validation::make($this)
        ->type('string')
        ->length(validation_between('login'))
        ->unique('assos', 'login')
        ->post('required')
        ->get(),
            'description' => Validation::make($this)
        ->type('string')
        ->length(validation_between('description'))
        ->post('required')
        ->get(),
            'type_id' => Validation::make($this)
        ->type('uuid')
        ->exists('assos_types', 'id')
        ->post('required')
        ->get(),
            'parent_id' => Validation::make($this)
        ->type('uuid')
        ->exists('assos', 'id')
        ->get(),
            'user_id' => Validation::make($this)
        ->type('uuid')
        ->exists('users', 'id')
        ->post('required')
        ->get(),
        ];
    }
}

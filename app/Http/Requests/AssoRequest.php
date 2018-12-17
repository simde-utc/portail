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

use Validation;
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
            'name' => Validation::type('string')
                ->length('name')
                ->unique('assos', 'name')
                ->post('required')
                ->get(),
            'shortname' => Validation::type('string')
                ->length('login')
                ->unique('assos', 'shortname')
                ->post('required')
                ->get(),
            'login' => Validation::type('string')
                ->length('login')
                ->unique('assos', 'login')
                ->post('required')
                ->get(),
            'image' => Validation::type('url')
                ->length('url')
                ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->post('required')
                ->get(),
            'type_asso_id' => Validation::type('uuid')
                ->exists('assos_types', 'id')
                ->post('required')
                ->get(),
            'parent_id' => Validation::type('uuid')
                ->exists('assos', 'id')
                ->get(),
            'user_id' => Validation::type('uuid')
                ->exists('users', 'id')
                ->post('required')
                ->get(),
        ];
    }
}

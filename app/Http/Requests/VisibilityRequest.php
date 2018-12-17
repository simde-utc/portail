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
use Illuminate\Foundation\Http\FormRequest;

class VisibilityRequest extends FormRequest
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
        $visibility = $this->visibility;

        return [
            'type' => Validation::type('string')
                ->length('name')
                ->unique('visibilities', 'type,'.$visibility->id)
                ->post('required')
                ->get(),
            'name' => Validation::type('string')
                ->length('name')
                ->unique('visibilities', 'name,'.$visibility->id)
                ->post('required')
                ->get(),
            'parent_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->get(),
        ];
    }
}

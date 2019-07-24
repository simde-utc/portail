<?php
/**
 * Association types request management.
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

class AssoTypeRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => Validation::type('string')
                ->length('name')
                ->unique('assos_types', 'type')
                ->post('required')
                ->get(),
            'name' => Validation::type('string')
                ->length('description')
                ->unique('assos_types', 'name')
                ->post('required')
                ->get(),
        ];
    }
}

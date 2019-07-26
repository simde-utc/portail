<?php
/**
 * Partners request management.
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
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => Validation::type('string')
                ->length('name')
                ->unique('partners', 'name')
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

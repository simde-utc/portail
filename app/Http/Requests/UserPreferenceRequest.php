<?php
/**
 * Preference by user request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class UserPreferenceRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => Validation::type('string')
                ->post('required')
                ->get(),
            'value' => Validation::type('string')
                ->post('required')
                ->patch('required')
                ->get(),
            'only_for' => Validation::type('string')
                ->post('required')
                ->get(),
        ];
    }
}

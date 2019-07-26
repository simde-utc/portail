<?php
/**
 * FAQ request management.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class FaqRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question' => Validation::type('string')
                ->length('title')
                ->unique('faqs', 'question')
                ->post('required')
                ->get(),
            'answer' => Validation::type('string')
                ->length('description')
                ->post('required')
                ->get(),
            'category_id' => Validation::type('uuid')
                ->exists('faqs_categories', 'id')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
        ];
    }
}

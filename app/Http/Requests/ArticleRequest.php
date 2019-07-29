<?php
/**
 * Articles request management.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class ArticleRequest extends Request
{
    /**
     * Define fields validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => Validation::type('string')
                ->length('title')
                ->post('required')
                ->get(),
            'content' => Validation::type('string')
                ->length('article')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->get(),
            'image' => Validation::type('image')
                ->length('url')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
            'event_id' => Validation::type('integer')
                ->exists('events', 'id')
                ->get(),
            'created_by_type' => Validation::type('string')
                ->get(),
            'created_by_id' => Validation::type('uuid')
                ->get(),
            'owned_by_type' => Validation::type('string')
                ->post('required')
                ->get(),
            'owned_by_id' => Validation::type('uuid')
                ->post('required')
                ->get(),
            'tags' => Validation::type('array')
                ->get()
        ];
    }
}

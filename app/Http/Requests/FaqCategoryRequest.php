<?php
/**
 * Gestion de la requête pour les catégories FAQs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;

class FaqCategoryRequest extends Request
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
                ->unique('faqs_categories', 'name')
                ->post('required')
                ->get(),
            'description' => Validation::type('string')
                ->length('description')
                ->post('required')
                ->get(),
            'parent_id' => Validation::type('uuid')
                ->exists('faqs_categories', 'id')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
        ];
    }
}

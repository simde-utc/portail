<?php
/**
 * Gestion de la requête pour les articles.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            'title' => Validation::make($this)
                ->type('string')
                ->length(validation_between('title'))
                ->post('required')
                ->get(),
            'content' => Validation::make($this)
                ->type('string')
                ->length(validation_between('article'))
                ->post('required')
                ->get(),
            'image' => Validation::make($this)
                ->type('image')
                ->length(validation_between('url'))
                ->get(),
            'toBePublished' => Validation::make($this)
                ->type('boolean')
                ->get(),
            'visibility_id' => Validation::make($this)
                ->type('integer')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
            'asso_id' => Validation::make($this)
                ->type('uuid')->exists('assos', 'id')
                ->post('required')
                ->get(),
        ];
    }
}

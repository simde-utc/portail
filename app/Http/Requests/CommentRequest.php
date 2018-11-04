<?php
/**
 * Gestion de la requête pour les commentaires.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\PortailException;
use App\Interfaces\Model\CanHaveComments;

class CommentRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur à le droit de faire cette requête.
     * Ici on détermine en particulier la ressource concernée par notre commentaire.
     *
     * @return boolean
     */
    public function authorize()
    {
        $class = \ModelResolver::getModelFromCategory($this->resource_type, CanHaveComments::class);

        $this->resource = $class::find($this->resource_id);

        return (bool) $this->resource;
    }

    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => Validation::make($this)
                ->type('string')
                ->length(validation_between('comment'))
                ->post('required')
                ->get(),
            'parent_id' => Validation::make($this)
                ->type('uuid')
                ->exists('comments', 'id')
                ->get(),
            'visibility_id' => Validation::make($this)
                ->type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
        ];
    }
}

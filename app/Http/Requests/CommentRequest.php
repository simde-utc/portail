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

use Validation;
use App\Exceptions\PortailException;
use App\Interfaces\Model\CanHaveComments;

class CommentRequest extends Request
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
            'body' => Validation::type('string')
                ->length('comment')
                ->post('required')
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
        ];
    }
}

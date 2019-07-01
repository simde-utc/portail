<?php
/**
 * Requête par défaut.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * Ajoute notre requête à la validation.
     */
    public function __construct()
    {
        parent::__construct();

        Validation::setRequest($this);
    }

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
     * Retourne la liste des règles de validation.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}

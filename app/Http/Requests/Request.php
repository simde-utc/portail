<?php
/**
 * Default request.
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
     * Add request to validation.
     * 
     */
    public function __construct()
    {
        parent::__construct();

        Validation::setRequest($this);
    }

    /**
     * 
     * Determine if the user has the right to make this request.
     * Everything is done in the controllers.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Return the validation rules list.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}

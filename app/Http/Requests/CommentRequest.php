<?php
/**
 * Comment request management.
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
     * Determine if the user has the right to make this request.
     * Here we determine in particular the resource concerned by the comment.
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
     * Define fields validation rules.
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
                ->post('required')
                ->get(),
            'created_by_id' => Validation::type('uuid')
                ->post('required')
                ->get(),
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\PortailException;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $class = \ModelResolver::getModelFromCategory($this->resource_type);

        $this->resource = $class::find($this->resource_id);
        
        return (bool) $this->resource;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' =>   Validation::make($this)
                        ->type('string')
                        ->length(validation_between('comment'))
                        ->post('required')
                        ->get(),
            'parent_id' => Validation::make($this)
                        ->type('integer')
                        ->exists('comments', 'id')
                        ->get(),
            'user_id' => Validation::make($this)
                        ->type('integer')
                        ->exists('users', 'id')
                        ->post('required')
                        ->get(),
            'visibility_id' => Validation::make($this)
                        ->type('integer')
                        ->exists('visibilities', 'id')
                        ->post('required')
                        ->get(),
            'commentable_id' => Validation::make($this)
                        ->type('integer')
                        ->post('required')
                        ->get(),
            'commentable_type' => Validation::make($this)
                        ->type('string')
                        ->length(validation_between('string'))
                        ->post('required')
                        ->get(),
        ];
    }
}

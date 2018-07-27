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
        $this->resource = \ModelResolver::getModelFromCategory($this->resource_type);
        
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
            'body' => Validation::make($this)
                        ->type('string'),
            'visibility_id' => Validation::make($this)
                        ->type('integer')
                        ->exists('visibilities', 'id')
                        ->post('required')
                        ->get(),
        ];
    }
}

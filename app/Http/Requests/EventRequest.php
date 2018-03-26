<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'         => 'required|string|between:1,64',
            'description'   => 'required|text',
            'image'         => 'string|between:1,191',
            'from'          => 'required|timestamp',
            'to'            => 'required|timestamp',
            'visibility_id'    => 'required|integer',
            'place'         => 'string'
        ];
    }
}

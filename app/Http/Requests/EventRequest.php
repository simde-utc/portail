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
            'title'         => 'string|between:1,64'.($this->isMethod('put')?'':'|required'),
            'description'   => 'required|string|between:10,800',
            //'image'         => 'string|between:1,191',
            //'from'          => 'timestamp'.($this->isMethod('put')?'':'|required'),
            'to'            => /*'timestamp'.*/($this->isMethod('put')?'':'|required'),
            //'visibility_id'    => 'string|between:1,128'.($this->isMethod('put')?'':'|required'),
            //'place'         => 'string',
        ];
    }
}

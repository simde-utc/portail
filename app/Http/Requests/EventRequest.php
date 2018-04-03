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
            'description'   => 'string|between:10,800',
            'image'         => 'nullable|image'.validation_between('url'),
            'from'          => 'date'.($this->isMethod('put')?'':'|required'),
            'to'            => 'date'.($this->isMethod('put')?'':'|required'),
            'visibility_id' => 'integer'.($this->isMethod('put')?'':'|required'),
            'place'         => 'nullable|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisibilityRequest extends FormRequest
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
        $id=$this->visibilities;

        return [

            'name' => 'unique:visibilities,name'.$id.'|string|between:3,191'.($this->isMethod('put')?'':'|required'),
            'type'  => 'unique:visibilities,type'.$id.'|string|between:3,191'.($this->isMethod('put')?'':'|required'),
            'parent_id' => 'integer|exists:visibilities,id'.($this->isMethod('put')?'':'|required'),
        ];
    }
}

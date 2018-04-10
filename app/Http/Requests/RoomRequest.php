<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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
	        'name' => Validation::make($this)->type('string')->length(validation_between('string'))->post('required')->get(),
	        'asso_id' => Validation::make($this)->type('integer')->exists('assos', 'id')->post('required')->get(),
        ];
    }
}

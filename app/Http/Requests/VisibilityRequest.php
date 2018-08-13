<?php

namespace App\Http\Requests;

use App\Facades\Validation;
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
    	$id = $this->visibility;
        return [
	        'name' => Validation::make($this)->type('string')->length('between:3,191')->unique('visibilities', 'name,'.$id)->post('required')->get(),
	        'type' => Validation::make($this)->type('string')->length('between:3,191')->unique('visibilities', 'type,'.$id)->post('required')->get(),
	        'parent_id' => Validation::make($this)->type('uuid')->exists('visibilities', 'id')->post('required')->get(),
        ];
    }
}

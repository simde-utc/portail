<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //TODO voir qui a le droit de gérer les partenaires.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
<<<<<<< HEAD
	        'name' => Validation::make($this)->type('string')->length(validation_between('name'))->post('required')->get(),
	        'description' => Validation::make($this)->type('string')->length(validation_between('description'))->post('required')->get(),
	        'imaeg' => Validation::make($this)->type('image')->length(validation_between('url'))->post('required')->get(),
=======
            'name' => 'string|'.validation_between('title').($this->isMethod('put')?'':'|required'),
	        'description' => 'string|'.validation_between('description').($this->isMethod('put')?'':'|required'),
	        'image'=> 'image'.validation_between('url').'|nullable', 
>>>>>>> Bug dans Partner résolu
        ];
    }
}


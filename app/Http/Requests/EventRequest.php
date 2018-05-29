<?php

namespace App\Http\Requests;

use App\Facades\Validation;
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
	        'title' => Validation::make($this)->type('string')->length(validation_between('title'))->post('required')->get(),
	        'description' => Validation::make($this)->type('string')->length(validation_between('article'))->post('required')->get(),
	        'image' => Validation::make($this)->type('image')->nullable()->length(validation_between('url'))->get(),
	        'from' => Validation::make($this)->type('date')->post('required')->get(),
	        'to' => Validation::make($this)->type('date')->post('required')->get(),
	        'visibility_id' => Validation::make($this)->type('integer')->exists('visibilities', 'id')->post('required')->get(),
	        'place' => Validation::make($this)->type('string')->nullable()->length(validation_between('string'))->get(),
        ];
    }
}

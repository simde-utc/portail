<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //TODO : vérifier que l'utilisateur a les droits
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
	        'content' => Validation::make($this)->type('string')->length(validation_between('article'))->post('required')->get(),
	        'image' => Validation::make($this)->type('image')->length(validation_between('url'))->get(),
	        'toBePublished' => Validation::make($this)->type('boolean')->get(),
	        'visibility_id' => Validation::make($this)->type('integer')->exists('visibilities','id')->post('required')->get(),
	        'asso_id' => Validation::make($this)->type('integer')->exists('assos', 'id')->post('required')->get(),
	        ];
    }
}

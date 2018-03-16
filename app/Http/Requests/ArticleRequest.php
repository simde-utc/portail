<?php

namespace App\Http\Requests;

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
			'title' => 'required|string|'.validation_between('title'),
	        'content' => 'required|string|'.validation_between('article'),
	        'image' => 'image|'.validation_between('url'),
	        'toBePublished' => 'boolean',
	        'visibility_id' => 'required|integer|exists:visibilities,id',
	        'asso_id' => 'required|integer|exists:assos,id',
        ];
    }
}

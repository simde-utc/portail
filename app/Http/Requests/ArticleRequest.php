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
			'title' => 'string|'.validation_between('title').($this->isMethod('put')?'':'|required'),
	        'content' => 'string|'.validation_between('article').($this->isMethod('put')?'':'|required'),
	        'image' => 'image|'.validation_between('url'),
	        'toBePublished' => 'boolean',
	        'visibility_id' => 'integer|exists:visibilities,id'.($this->isMethod('put')?'':'|required'),
	        'asso_id' => 'integer|exists:assos,id'.($this->isMethod('put')?'':'|required'),
        ];
    }
}

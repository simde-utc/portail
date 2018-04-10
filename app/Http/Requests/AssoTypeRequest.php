<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;

class AssoTypeRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;				// TODO : changer pour savoir si l'utilisateur à les droits
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'name' => Validation::make($this)->type('string')->length(validation_between('name'))->post('required')->get(),
			'description' => Validation::make($this)->type('string')->length(validation_between('description'))->post('required')->get(),
		];
	}
}

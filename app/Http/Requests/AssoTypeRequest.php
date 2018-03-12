<?php

namespace App\Http\Requests;

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
			'name' 			=> 'required|string|between:3,191',
			'description' 	=> 'required|string|between:3,800',
		];
	}
}

<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;

class AssoRequest extends FormRequest
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
			'name' => Validation::make($this)
						->type('string')
						->length(validation_between('name'))
						->unique('assos', 'name')
						->post('required')
						->get(),
			'shortname' => Validation::make($this)
						->type('string')
						->length(validation_between('login'))
						->unique('assos', 'shortname')
						->post('required')
						->get(),
			'login' => Validation::make($this)
						->type('string')
						->length(validation_between('login'))
						->unique('assos', 'login')
						->post('required')
						->get(),
			'description' => Validation::make($this)
						->type('string')
						->length(validation_between('description'))
						->post('required')
						->get(),
			'type_asso_id' => Validation::make($this)
						->type('integer')
						->exists('assos_types', 'id')
						->post('required')
						->get(),
			'parent_id' => Validation::make($this)
						->type('integer')
						->exists('assos', 'id')
						->get(),
			'user_id' => Validation::make($this)
						->type('integer')
						->exists('users', 'id')
						->post('required')
						->get(),
		];
	}
}

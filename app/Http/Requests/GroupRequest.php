<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $group = Group::find($this->route('group'));

        return $group && $group->is_active && $group->user_id = $this->user();
        // true si ce groupe existe, il est actif et l'utilisateur est le créateur du groupe.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // TODO: Vielles valeurs de la BDD, pas oublier de changer.
        return [
            'name'          => 'required|string|between:1,64',
            'icon'          => 'required|string|between:3,191',
            'is_public'     => 'required|boolean'
        ];
    }
}

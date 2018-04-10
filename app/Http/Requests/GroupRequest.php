<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;

class GroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO: Autoriser en PUT et DELETE si c'est l'owner.

        return true;
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
            'name'          => 'string|between:1,64'.($this->isMethod('put')?'':'|required'),
            'icon'          => 'string|between:3,191'.($this->isMethod('put')?'':'|required'),
            'visibility'    => 'string|between:1,128'.($this->isMethod('put')?'':'|required'),
            'is_active'     => 'boolean'.($this->isMethod('put')?'':'|required'),
        ];
    }
}

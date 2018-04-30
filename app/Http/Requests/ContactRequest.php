<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Asso;
use App\Models\User;
use App\Exceptions\PortailException;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->type == 'assos') {
            $this->model = Asso::class;
            return true;
        } else if ($this->type == 'users') {
            $this->model = User::class;
            return true;
        } else {
            throw new PortailException('La ressource indiquée n\'a pas été reconnue.');
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}

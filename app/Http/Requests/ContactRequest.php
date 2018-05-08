<?php

namespace App\Http\Requests;

use App\Facades\Validation;
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
        // TODO (Natan) : régler et/ou tester sécurité si un utilisateur set lui-même $request->model à un nom de classe valable.
        // A discuter : utile/correct de throw une exception ? Ou on retourne juste false ?
        if ($this->resource_type == 'assos') {     
            $this->model = Asso::class;
        } else if ($this->resource_type == 'users') {
            $this->model = User::class;
        } else {
            throw new PortailException('La ressource indiquée n\'a pas été reconnue.');
            return false;
        }

        // Vérifier si on trouve la ressource.
        if ($this->resource = $this->model::find($this->resource_id)) {
            return true;
        }
        else {
            throw new PortailException('Vous ne pouvez créer des données de contact uniquement pour des ressources existantes.');
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
            'body' => Validation::make($this)
                        ->type('string')
                        ->length(validation_between('string'))
                        ->post('required')
                        ->get(),
            'description' => Validation::make($this)
                        ->type('string')
                        ->length(validation_between('string'))
                        ->nullable()
                        ->post('required')
                        ->get(),
            'contact_type_id' => Validation::make($this)
                        ->type('integer')
                        ->exists('contacts_types', 'id')
                        ->post('required')
                        ->get(),
        ];
    }
}

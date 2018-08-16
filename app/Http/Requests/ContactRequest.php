<?php

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Asso;
use App\Models\User;
use App\Exceptions\PortailException;
use App\Interfaces\CanHaveContacts;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->resource_type)
            $this->resource = \ModelResolver::getModelFromCategory($this->resource_type, CanHaveContacts::class)->find($this->resource_id);
        else // On est sur /user/contacts
            $this->resource = \Auth::user();

        return (bool) $this->resource;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => Validation::make($this)
                        ->type('string')
                        ->length(validation_between('name'))
                        ->post('required')
                        ->get(),
            'value' => Validation::make($this)
                        ->type('string')
                        ->nullable()
                        ->post('required')
                        ->get(),
            'contact_type_id' => Validation::make($this)
                        ->type('uuid')
                        ->exists('contacts_types', 'id')
                        ->post('required')
                        ->get(),
            'visibility_id' => Validation::make($this)
                        ->type('uuid')
                        ->exists('visibilities', 'id')
                        ->post('required')
                        ->get(),
        ];
    }
}

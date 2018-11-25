<?php
/**
 * Gestion de la requête pour les contacts.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Asso;
use App\Models\User;
use App\Exceptions\PortailException;
use App\Interfaces\Model\CanHaveContacts;

class ContactRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur à le droit de faire cette requête.
     * Ici on détermine en particulier la ressource concernée par notre contact.
     *
     * @return boolean
     */
    public function authorize()
    {
        if ($this->resource_type) {
            $this->resource = \ModelResolver::findModelFromCategory(
                $this->resource_type, $this->resource_id, CanHaveContacts::class
            );
        } else {
            // On est sur /user/contacts.
            $this->resource = \Auth::user();
        }

        return (bool) $this->resource;
    }

    /**
     * Défini les règles de validation des champs.
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

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

use Validation;
use App\Models\Asso;
use App\Models\User;
use App\Exceptions\PortailException;
use App\Interfaces\Model\CanHaveContacts;

class ContactRequest extends Request
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
            'name' => Validation::type('string')
                ->length('name')
                ->post('required')
                ->get(),
            'value' => Validation::type('string')
                ->nullable()
                ->post('required')
                ->get(),
            'contact_type_id' => Validation::type('uuid')
                ->exists('contacts_types', 'id')
                ->post('required')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
            'owned_by_type' => Validation::type('string')
                ->post('required')
                ->get(),
            'owned_by_id' => Validation::type('uuid')
                ->post('required')
                ->get(),
        ];
    }
}

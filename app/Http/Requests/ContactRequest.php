<?php
/**
 * Contacts request management.
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
     * Determine if the user has the right to make this request.
     * Here we determine in particular the resource concerned by the contact.
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
            // We are on /user/contacts.
            $this->resource = \Auth::user();
        }

        return (bool) $this->resource;
    }

    /**
     * Define fields validation rules.
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

<?php
/**
 * Gestion de la requête pour les permissions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use App\Exceptions\PortailException;

class PermissionAssignmentRequest extends Request
{
    /**
     * Détermine si l'utilisateur à le droit de faire cette requête.
     * On détermine la ressource concernée et l'utilisateur.
     *
     * @return boolean
     */
    public function authorize()
    {
        if ($this->resource_type) {
            $this->resource = \ModelResolver::getModelFromCategory($this->resource_type)->find($this->resource_id);
        } else {
            // On est sur /user/permissions ou /users/{user_id}/permissions.
            $this->resource = \Auth::user();
        }

        if (!$this->user_id) {
            $this->user_id = \Auth::id();
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
            'user_id' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'validated_by' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'permission_id' => Validation::type('uuid')
                ->exists('permissions', 'id')
                ->get(),
            'semester_id' => Validation::type('uuid')
                ->exists('semesters', 'id')
                ->get(),
        ];
    }
}

<?php
/**
 * Gestion de la requête pour les articles.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Requests;

use Validation;
use App\Models\Group;

class GroupRequest extends Request
{
    /**
     * Détermine si l'utilisateur à le droit de faire cette requête.
     * On vérifie que le groupe est bien géré par le propriétaire.
     *
     * @return boolean
     */
    public function authorize()
    {
        if ($this->isMethod('put') || $this->isMethod('patch') || $this->isMethod('delete')) {
            $group = Group::find($this->route('group'));

            return $group && \Auth::id() && $group->id === \Auth::id();
        } else {
            return true;
        }
    }

    /**
     * Défini les règles de validation des champs.
     *
     * @return array
     */
    public function rules()
    {
        $group = $this->group;

        return [
            'name' => Validation::type('string')
                ->length('name')
                ->unique('groups', 'name')
                ->post('required')
                ->get(),
            'icon' => Validation::type('image')
                ->length('url')
                ->nullable()
                ->get(),
            'user_id' => Validation::type('uuid')
                ->exists('users', 'id')
                ->get(),
            'visibility_id' => Validation::type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
        ];
    }
}

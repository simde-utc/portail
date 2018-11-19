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

use App\Facades\Validation;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;
use App\Services\Visible\Visible;

class GroupRequest extends FormRequest
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
            'name' => Validation::make($this)
                ->type('string')
                ->length(validation_between('name'))
                ->unique('groups', 'name,'.$group->id)
                ->post('required')
                ->get(),
            'icon' => Validation::make($this)
                ->type('image')
                ->length(validation_between('url'))
                ->nullable()
                ->get(),
            'visibility_id' => Validation::make($this)
                ->type('uuid')
                ->exists('visibilities', 'id')
                ->post('required')
                ->get(),
            'is_active' => Validation::make($this)
                ->type('boolean')
                ->get(),
        ];
    }
}

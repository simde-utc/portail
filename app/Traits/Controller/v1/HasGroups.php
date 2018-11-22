<?php
/**
 * Ajoute au controlleur un accès aux groupes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasVisibility;

trait HasGroups
{
    use HasVisibility;

    /**
     * Indique que l'utilisateur est membre de l'instance.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isPrivate(string $user_id, $model=null)
    {
        if ($model === null) {
            return false;
        }

        if ($model->user_id && $model->user_id == $user_id) {
            return true;
        }

        try {
            return $model->hasOneMember(\Auth::id());
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Renvoie le groupe demandé.
     *
     * @param Request $request
     * @param string  $group_id
     * @return Group
     */
    protected function getGroup(Request $request, string $group_id): Group
    {
        $group = Group::find($group_id);

        if ($group) {
            if ($this->isVisible($group)) {
                return $group;
            } else {
                abort(403, 'Vous n\'avez pas le droit de voir le groupe');
            }
        }

        abort(404, 'Groupe non trouvé');
    }
}

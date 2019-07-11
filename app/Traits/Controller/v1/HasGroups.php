<?php
/**
 * Adds the controller an access to Groups.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Asso;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasGroups
{
    /**
     * Return the requested group.
     *
     * @param Request $request
     * @param string  $group_id
     * @return Group
     */
    protected function getGroup(Request $request, string $group_id): Group
    {
        $group = Group::findSelection($group_id);

        if ($group) {
            return $group;
        }

        abort(404, 'Groupe non trouvé');
    }
}

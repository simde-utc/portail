<?php
/**
 * Ajoute au controlleur un accès aux rôles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Models\Role;
use App\Models\Model;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;

trait HasRoles
{
    use HasSemesters;

    /**
     * Récupère un rôle.
     *
     * @param  Request $request
     * @param  string  $role_id
     * @param  string  $verb
     * @return Role|null
     */
    protected function getRole(Request $request, string $role_id, string $verb='get')
    {
        $role = Role::where('id', $role_id)->firstSelection();

        if ($role) {
            if (!$this->tokenCanSee($request, $role, $verb)) {
                abort(403, 'L\'application n\'a pas les droits sur ce rôle');
            }

            if ($verb !== 'get' && !$role->owned_by->isRoleManageableBy(\Auth::id())) {
                abort(403, 'Vous n\'avez pas les droits suffisants');
            }

            return $role;
        }

        abort(404, 'Impossible de trouver la rôle');
    }

    /**
     * Récupère un rôle depuis un utlisateur
     *
     * @param  Request $request
     * @param  User    $user
     * @param  string  $role_id
     * @return Role|null
     */
    protected function getRoleFromUser(Request $request, User $user, string $role_id)
    {
        $semester_id = $this->getSemester($request->input('semester_id'))->id;

        $role = $user->getUserRoles(null, $semester_id)->first(function ($role) use ($role_id) {
            return $role->id === $role_id;
        });

        if ($role) {
            return $role;
        } else {
            abort(404, 'Cette personne ne possède pas ce rôle');
        }
    }

    /**
     * Indique si le token peut voir ou non.
     *
     * @param  Request $request
     * @param  Role    $role
     * @param  string  $verb
     * @return boolean
     */
    protected function tokenCanSee(Request $request, Role $role, string $verb='get')
    {
        $scopeHead = \Scopes::getTokenType($request);

        if (!\Scopes::has($request, 'user-'.$verb.'-roles-'.\ModelResolver::getCategory($role->owned_by_type).'-owned')) {
            return false;
        }

        if (\Scopes::isUserToken($request)) {
            $functionToCall = 'isRole'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

            return $role->owned_by->$functionToCall(\Auth::id());
        } else {
            return true;
        }
    }
}

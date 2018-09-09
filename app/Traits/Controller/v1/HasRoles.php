<?php

namespace App\Traits\Controller\v1;

use App\Models\Role;
use App\Models\Model;
use App\Models\User;
use Illuminate\Http\Request;

trait HasRoles
{
	protected function getRole(Request $request, string $id, string $verb = 'get') {
		$role = Role::where('id', $id)->first();

		if ($role) {
			if (!$this->tokenCanSee($request, $role, $verb))
				abort(403, 'L\'application n\'a pas les droits sur ce rôle');

			if ($verb !== 'get' && !$role->owned_by->isRoleManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $role;
		}

		abort(404, 'Impossible de trouver la rôle');
	}

	protected function tokenCanSee(Request $request, Role $role, string $verb = 'get') {
		$scopeHead = \Scopes::getTokenType($request);

		if (!\Scopes::has($request, 'user-'.$verb.'-roles-'.\ModelResolver::getCategory($role->owned_by_type).'-owned'))
			return false;

		if (\Scopes::isUserToken($request)) {
			$functionToCall = 'isRole'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

			return $role->owned_by->$functionToCall(\Auth::id());
		}
		else
			return true;
	}
}

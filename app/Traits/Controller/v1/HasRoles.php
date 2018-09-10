<?php

namespace App\Traits\Controller\v1;

use App\Models\Role;
use App\Models\Model;
use App\Models\User;
use App\Models\Semester;
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

	protected function getRoleFromUser(Request $request, User $user, string $role_id) {
		$semester_id = Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id;

		$role = $user->roles()->wherePivot('role_id', $role_id)
			->wherePivot('semester_id', $semester_id)
			->withPivot(['semester_id', 'validated_by'])->first();

		if ($role) {
			$role->semester_id = $role->pivot->semester_id;
			$role->validated_by = $role->pivot->validated_by;

			return $role->makeHidden('pivot');
		}
		else
			abort(404, 'Cette personne ne possède pas ce rôle');
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

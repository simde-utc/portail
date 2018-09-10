<?php

namespace App\Traits\Controller\v1;

use App\Models\Permission;
use App\Models\Model;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;

trait HasPermissions
{
	protected function getPermission(Request $request, string $id, string $verb = 'get') {
		$permission = Permission::where('id', $id)->first();

		if ($permission) {
			if (!$this->tokenCanSee($request, $permission, $verb))
				abort(403, 'L\'application n\'a pas les droits sur ce permission');

			if ($verb !== 'get' && !$permission->owned_by->isPermissionManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants');

			return $permission;
		}

		abort(404, 'Impossible de trouver la permission');
	}

	protected function getPermissionFromUser(Request $request, User $user, string $permission_id) {
		$semester_id = Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id;

		$permission = $user->permissions()->wherePivot('permission_id', $permission_id)
			->wherePivot('semester_id', $semester_id)
			->withPivot(['semester_id', 'validated_by'])->first();

		if ($permission) {
			$permission->semester_id = $permission->pivot->semester_id;
			$permission->validated_by = $permission->pivot->validated_by;

			return $permission->makeHidden('pivot');
		}
		else
			abort(404, 'Cette personne ne possède pas ce permission');
	}

	protected function tokenCanSee(Request $request, Permission $permission, string $verb = 'get') {
		$scopeHead = \Scopes::getTokenType($request);

		if (!\Scopes::has($request, 'user-'.$verb.'-permissions-'.\ModelResolver::getCategory($permission->owned_by_type).'-owned'))
			return false;

		if (\Scopes::isUserToken($request)) {
			$functionToCall = 'isPermission'.($verb === 'get' ? 'Accessible' : 'Manageable').'By';

			return $permission->owned_by->$functionToCall(\Auth::id());
		}
		else
			return true;
	}
}

<?php

namespace App\Traits\Controller\v1;

use App\Models\Permission;
use App\Models\User;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasPermissions
{
	protected function getPermission(Request $request, string $id, string $verb = 'get') {
		$permission = Permission::where('id', $id)->first();

		if ($permission) {
			if (!$this->tokenCanSee($request, $permission, $verb))
				abort(403, 'L\'application n\'a pas les droits sur cette permission');

			if ($verb !== 'get' && \Auth::id() && !$permission->owned_by->isPermissionManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants pour gérer cette permission');

			return $permission;
		}

		abort(404, 'Impossible de trouver la permission');
	}

	protected function getPermissionsFromModel(Request $request) {
		$semester_id = Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id;
		$choices = $this->getChoices($request, ['owned', 'herited']);

		if (count($choices) === 2)
			$method = 'getUserPermissions';
		else if (in_array('owned', $choices))
			$method = 'getUserPermissionsFromHasPermissions';
		else
			$method = 'getUserPermissionsFromRoles';

		return $request->resource->$method($request->user_id, $semester_id);
	}

	protected function getPermissionFromModel(Request $request, string $permission_id, string $verb = 'get') {
		$permission = $this->getPermissionsFromModel($request)->find($permission_id);

		if ($permission) {
			if (!$this->tokenCanSee($request, $permission, $verb))
				abort(403, 'L\'application n\'a pas les droits sur cette permission');

			if ($verb !== 'get' && \Auth::id() && !$permission->owned_by->isPermissionManageableBy(\Auth::id()))
				abort(403, 'Vous n\'avez pas les droits suffisants pour gérer cette permission');

			return $permission;
		}
		else
			abort(404, 'Cette instance ne possède pas cette permission');
	}

	protected function checkTokenRights(Request $request, string $verb = 'get') {
		if (!\Scopes::hasOne($request, \Scopes::getTokenType($request).'-'.$verb.'-permissions-'.\ModelResolver::getCategory($request->resource)))
			abort(503, 'L\'application n\'a pas le droit de voir les permissions de cette ressource');
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

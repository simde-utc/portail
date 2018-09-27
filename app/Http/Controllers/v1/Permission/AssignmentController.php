<?php

namespace App\Http\Controllers\v1\Permission;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Semester;
use App\Http\Requests\PermissionRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasPermissions;

class AssignmentController extends Controller
{
	use HasPermissions;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-permissions', 'client-get-permissions'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-permissions', 'client-create-permissions'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-edit-permissions', 'client-edit-permissions'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-remove-permissions', 'client-remove-permissions'),
			['only' => ['destroy']]
		);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @param string|null $user_id
	 * @return JsonResponse
	 */
	public function index(PermissionRequest $request): JsonResponse {
		$this->checkTokenRights($request);

		$permissions = $this->getPermissionsFromModel($request)
			->map(function ($permission) {
				return $permission->hideData();
			});

		return response()->json($permissions, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\PermissionRequest $request
	 * @param string|null $user_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function store(PermissionRequest $request): JsonResponse {
		$this->checkTokenRights($request, 'create');

		$semester_id = Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id;

	 	$request->resource->assignPermissions($request->input('permission_id'), [
			'user_id' => \Auth::id() ?? $request->input('user_id'),
			'validated_by' => \Auth::id() ?? $request->input('validated_by'),
			'semester_id' => $semester_id
		], \Scopes::isClientToken($request));

		$permission = $this->getPermissionFromModel($request, $request->input('permission_id'));

		return response()->json($permission->hideSubData());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param PermissionRequest $request
	 * @param string $user_id
	 * @param string|null $permission_id
	 * @return JsonResponse
	 */
	public function show(PermissionRequest $request): JsonResponse {
		$this->checkTokenRights($request);

		if (is_null($permission_id))
			list($user_id, $permission_id) = [$permission_id, $user_id];

		$permission = $this->getPermissionFromModel($request, $request->permission);

		return response()->json($permission->hideSubData());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\PermissionRequest $request
	 * @param string $user_id
	 * @param string|null $permission_id
	 * @return void
	 */
	public function update(PermissionRequest $request) {
		abort(405, 'Impossible de modifier l\'assignation d\'un permission');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param PermissionRequest $request
	 * @param string $user_id
	 * @param string|null $permission_id
	 * @return void
	 * @throws PortailException
	 */
	public function destroy(PermissionRequest $request) {
		$this->checkTokenRights($request, 'remove');

		$permission = $this->getPermissionFromUser($request, $request->permission, 'remove');

		$user->removePermissions($permission_id, [
			'user_id' => \Auth::id() ?? $request->input('user_id'),
			'semester_id' => $permission->pivot->semester_id,
		], \Auth::id(), \Scopes::isClientToken($request));
	}
}

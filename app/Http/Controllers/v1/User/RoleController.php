<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Semester;
use App\Http\Requests\UserRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasUsers;
use App\Traits\Controller\v1\HasRoles;

class RoleController extends Controller
{
	use HasUsers, HasRoles;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-roles-users-assigned', 'client-get-roles-users-assigned'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-roles-users-assigned', 'client-create-roles-users-assigned'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-edit-roles-users-assigned', 'client-edit-roles-users-assigned'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-remove-roles-users-assigned', 'client-remove-roles-users-assigned'),
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
	public function index(Request $request, string $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id, true);
		$semester_id = Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id;

		$roles = $user->roles()->wherePivot('semester_id', $semester_id)
			->withPivot('semester_id', 'validated_by')->getSelection()
			->map(function ($role) {
				return $role->hideData();
			});

		return response()->json($roles, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param string|null $user_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function store(Request $request, string $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id, true);

	 	$user->assignRoles($request->input('role_id'), [
			'validated_by' => \Auth::id() ?? $request->input('validated_by'),
			'semester_id' => Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id
		], \Scopes::isClientToken());
		$role = $this->getRoleFromUser($request, $user, $request->input('role_id'));

		return response()->json($role->hideSubData());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param string $user_id
	 * @param string|null $role_id
	 * @return JsonResponse
	 */
	public function show(Request $request, string $user_id, string $role_id = null): JsonResponse {
		if (is_null($role_id))
			list($user_id, $role_id) = [$role_id, $user_id];

		$user = $this->getUser($request, $user_id, true);
		$role = $this->getRoleFromUser($request, $user, $role_id);

		return response()->json($role->hideSubData());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param string $user_id
	 * @param string|null $role_id
	 * @return void
	 */
	public function update(Request $request, string $user_id, string $role_id = null) {
		abort(405, 'Impossible de modifier l\'assignation d\'un rôle');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param string $user_id
	 * @param string|null $role_id
	 * @return void
	 * @throws PortailException
	 */
	public function destroy(Request $request, string $user_id, string $role_id = null) {
		if (is_null($role_id))
			list($user_id, $role_id) = [$role_id, $user_id];

		$user = $this->getUser($request, $user_id, true);
		$role = $this->getRoleFromUser($request, $user, $role_id);

		$user->removeRoles($role_id, [
			'semester_id' => Semester::getSemester($request->input('semester'))->id ?? Semester::getThisSemester()->id,
		], \Auth::id(), \Scopes::isClientToken());
	}
}

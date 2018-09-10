<?php

namespace App\Http\Controllers\v1\Permission;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Semester;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasPermissions;
use App\Traits\Controller\v1\HasOwners;

/**
 * Gestion des groupes utilisateurs
 *
 * @resource Permission
 */
class PermissionController extends Controller
{
	use HasPermissions, HasOwners;
	/**
	 * Scopes Permission
	 *
	 * Les Scopes requis pour manipuler les Permissions
	 */
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
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function index(Request $request): JsonResponse {
		$permissions = Permission::getSelection()->filter(function ($permission) use ($request) {
			return $this->tokenCanSee($request, $permission);
		})->values()->map(function ($permission) {
			return $permission->hideData();
		});

		return response()->json($permissions, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function store(Request $request): JsonResponse {
		$inputs = $request->all();
		$owner = $this->getOwner($request, 'permission', 'permission', 'create');

		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$permission = Permission::create($inputs);

		if ($permission)
			return response()->json($permission->hideSubData(), 201);
		else
			abort(500, 'Impossible de créer le permission');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, string $id): JsonResponse {
		$permission = $this->getPermission($request, $id);
		$permission->nbr_assigned = $permission->users()->where('semester_id', Semester::getThisSemester()->id)->count();

		return response()->json($permission->hideSubData(), 200);
	}

	/**
	 * Update Permission
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function update(Request $request, string $id): JsonResponse {
		$permission = $this->getPermission($request, $id, 'edit');

		if ($permission->update($request->input())) {
			$permission->nbr_assigned = $permission->users()->where('semester_id', Semester::getThisSemester()->id)->count();

			return response()->json($permission->hideSubData(), 200);
		}
		else
			abort(500, 'Impossible de créer le permission');
	}

	/**
	 * Delete Permission
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, string $id): JsonResponse {
		$permission = $this->getPermission($request, $id, 'manage');

		if ($permission->isDeletable()) {
			if ($permission->delete())
				abort(204);
			else
				abort(500, "Impossible de supprimer le permission souhaité");
		}
		else
			abort(403, "Il n'est pas autorisé de supprimer ce permission (possiblement car déjà assigné ou permissions enfants attachés)");
	}
}

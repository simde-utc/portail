<?php

namespace App\Http\Controllers\v1\Role;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Semester;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasRoles;
use App\Traits\Controller\v1\HasOwners;

/**
 * Gestion des groupes utilisateurs
 *
 * @resource Role
 */
class RoleController extends Controller
{
	use HasRoles, HasOwners;
	/**
	 * Scopes Role
	 *
	 * Les Scopes requis pour manipuler les Roles
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-roles', 'client-get-roles'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-roles', 'client-create-roles'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-edit-roles', 'client-edit-roles'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-remove-roles', 'client-remove-roles'),
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
		$roles = Role::getSelection()->filter(function ($role) use ($request) {
			return $this->tokenCanSee($request, $role);
		})->values()->map(function ($role) {
			return $role->hideData();
		});

		return response()->json($roles, 200);
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
		$owner = $this->getOwner($request, 'role', 'rôle', 'create');

		$inputs['owned_by_id'] = $owner->id;
		$inputs['owned_by_type'] = get_class($owner);

		$role = Role::create($inputs);

		if ($role) {
			if ($request->filled('parent_ids'))
				$role->assignParentRole($request->parent_ids);

			return response()->json($role->hideSubData(), 201);
		}
		else
			abort(500, 'Impossible de créer le role');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(Request $request, string $id): JsonResponse {
		$role = $this->getRole($request, $id);
		$role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

		return response()->json($role->hideSubData(), 200);
	}

	/**
	 * Update Role
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function update(Request $request, string $id): JsonResponse {
		$role = $this->getRole($request, $id, 'edit');

		if ($role->update($request->input())) {
			if ($request->filled('parent_ids'))
				$role->syncParentRole($request->parent_ids); // Attention ! Ici on change tous ses parents

			$role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

			return response()->json($role->hideSubData(), 200);
		}
		else
			abort(500, 'Impossible de créer le role');
	}

	/**
	 * Delete Role
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, string $id): JsonResponse {
		$role = $this->getRole($request, $id, 'manage');

		if ($role->isDeletable()) {
			if ($role->delete())
				abort(204);
			else
				abort(500, "Impossible de supprimer le role souhaité");
		}
		else
			abort(403, "Il n'est pas autorisé de supprimer ce rôle (possiblement car déjà assigné ou rôles enfants attachés)");
	}
}

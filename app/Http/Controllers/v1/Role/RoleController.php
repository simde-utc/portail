<?php
/**
 * Manages roles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Role;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Models\Semester;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasRoles;
use App\Traits\Controller\v1\HasOwners;

class RoleController extends Controller
{
    use HasRoles, HasOwners;

    /**
     * Must be able to manage roles.
     */
    public function __construct()
    {
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
     * Lists roles.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $roles = Role::getSelection()->filter(function ($role) use ($request) {
            return $this->tokenCanSee($request, $role);
        })->values()->map(function ($role) {
            return $role->hideData();
        });

        return response()->json($roles, 200);
    }

    /**
     * Adds a role.
     *
     * @param RoleRequest $request
     * @return JsonResponse
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $inputs = $request->all();
        $owner = $this->getOwner($request, 'role', 'rôle', 'create');

        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        $role = Role::create($inputs);

        if ($request->filled('parent_ids') || $request->filled('parent_id')) {
            $role->assignParentRole(($request->parent_ids ?? [$request->parent_id]));
        }

        return response()->json($role->hideSubData(), 201);
    }

    /**
     * Shows a role.
     *
     * @param Request $request
     * @param string 	$role_id
     * @return JsonResponse
     */
    public function show(Request $request, string $role_id): JsonResponse
    {
        $role = $this->getRole($request, $role_id);
        $role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

        return response()->json($role->hideSubData(), 200);
    }

    /**
     * Updates a role.
     *
     * @param RoleRequest $request
     * @param string      $role_id
     * @return JsonResponse
     */
    public function update(RoleRequest $request, string $role_id): JsonResponse
    {
        $role = $this->getRole($request, $role_id, 'edit');

        if ($role->update($request->input())) {
            if ($request->filled('parent_ids') || $request->filled('parent_id')) {
                // WARNING: Ici on change tous ses parents.
                $role->syncParentRole(($request->parent_ids ?? [$request->parent_id]));
            }

            $role->nbr_assigned = $role->users()->where('semester_id', Semester::getThisSemester()->id)->count();

            return response()->json($role->hideSubData(), 200);
        } else {
            abort(500, 'Impossible de créer le role');
        }
    }

    /**
     * Deletes a role.
     *
     * @param Request $request
     * @param string 	$role_id
     * @return void
     */
    public function destroy(Request $request, string $role_id): void
    {
        $role = $this->getRole($request, $role_id, 'manage');

        if ($role->isDeletable()) {
            if ($role->delete()) {
                abort(204);
            } else {
                abort(500, "Impossible de Deletesr le role souhaité");
            }
        } else {
            abort(403, "Il n'est pas autorisé de Deletesr ce rôle (possiblement car déjà assigné ou rôles enfants attachés)");
        }
    }
}

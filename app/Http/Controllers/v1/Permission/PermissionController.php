<?php
/**
 * Gère les permissions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Permission;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Semester;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasPermissions;
use App\Traits\Controller\v1\HasOwners;
use App\Http\Requests\PermissionRequest;

class PermissionController extends Controller
{
    use HasPermissions, HasOwners;

    /**
     * Nécessité de gérer les permissions.
     */
    public function __construct()
    {
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
     * Liste des permissions.
     *
     * @param  PermissionRequest $request
     * @return JsonResponse
     */
    public function index(PermissionRequest $request): JsonResponse
    {
        $permissions = Permission::getSelection()->filter(function ($permission) use ($request) {
            return $this->tokenCanSee($request, $permission);
        })->values()->map(function ($permission) {
            return $permission->hideData();
        });

        return response()->json($permissions, 200);
    }

    /**
     * Crée une permission.
     *
     * @param  PermissionRequest $request
     * @return JsonResponse
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        $inputs = $request->all();
        $owner = $this->getOwner($request, 'permission', 'permission', 'create');

        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        $permission = Permission::create($inputs);

        return response()->json($permission->hideSubData(), 201);
    }

    /**
     * Montre une permission.
     *
     * @param  PermissionRequest $request
     * @param  string 	$permission_id
     * @return JsonResponse
     */
    public function show(PermissionRequest $request, string $permission_id): JsonResponse
    {
        $permission = $this->getPermission($request, $permission_id);
        $permission->nbr_assigned = $permission->users()->where('semester_id', Semester::getThisSemester()->id)->count();

        return response()->json($permission->hideSubData(), 200);
    }

    /**
     * Met à jour une permission.
     *
     * @param  PermissionRequest $request
     * @param  string 	$permission_id
     * @return JsonResponse
     */
    public function update(PermissionRequest $request, string $permission_id): JsonResponse
    {
        $permission = $this->getPermission($request, $permission_id, 'edit');

        if ($permission->update($request->input())) {
            $permission->nbr_assigned = $permission->users()->where('semester_id', Semester::getThisSemester()->id)->count();

            return response()->json($permission->hideSubData(), 200);
        } else {
            abort(500, 'Impossible de créer le permission');
        }
    }

    /**
     * Supprime une permission.
     *
     * @param  PermissionRequest $request
     * @param  string 	$permission_id
     * @return void
     */
    public function destroy(PermissionRequest $request, string $permission_id): void
    {
        $permission = $this->getPermission($request, $permission_id, 'manage');

        if ($permission->isDeletable()) {
            if ($permission->delete()) {
                abort(204);
            } else {
                abort(500, "Impossible de supprimer le permission souhaité");
            }
        } else {
            abort(403, "Il n'est pas autorisé de supprimer cettte permission (déjà assignée ou utilisée)");
        }
    }
}

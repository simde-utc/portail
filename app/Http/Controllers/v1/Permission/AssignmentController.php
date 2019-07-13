<?php
/**
 * Manages assigned permissions.
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
use App\Models\User;
use App\Http\Requests\PermissionAssignmentRequest;
use App\Models\Visibility;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasPermissions;

class AssignmentController extends Controller
{
    use HasPermissions;

    /**
     * Must be able to manage assigned permissions.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-get-permissions', 'client-get-permissions'),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-create-permissions', 'client-create-permissions'),
	        ['only' => ['create']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-edit-permissions', 'client-edit-permissions'),
	        ['only' => ['edit']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-remove-permissions', 'client-remove-permissions'),
	        ['only' => ['remove']]
        );
    }

    /**
     * Lists assigned permissions.
     *
     * @param  PermissionAssignmentRequest $request
     * @return JsonResponse
     */
    public function index(PermissionAssignmentRequest $request): JsonResponse
    {
        $this->checkTokenRights($request);

        $permissions = $this->getPermissionsFromModel($request)
            ->map(function ($permission) {
                return $permission->hideData();
            });

        return response()->json($permissions, 200);
    }

    /**
     * Assigns a permission.
     *
     * @param  PermissionAssignmentRequest $request
     * @return JsonResponse
     */
    public function store(PermissionAssignmentRequest $request): JsonResponse
    {
        $this->checkTokenRights($request, 'create');
        $semester_id = $this->getSemester($request->input('semester_id'))->id;

        $request->resource->assignPermissions($request->input('permission_id'), [
            'user_id' => (\Auth::id() ?? $request->input('user_id')),
            'validated_by_id' => (\Auth::id() ?? $request->input('validated_by_id')),
            'semester_id' => $semester_id
        ], \Scopes::isClientToken($request));

        $permission = $this->getPermissionFromModel($request, $request->input('permission_id'));

        return response()->json($permission->hideSubData());
    }

    /**
     * Shows an assigned permission.
     *
     * @param  PermissionAssignmentRequest $request
     * @return JsonResponse
     */
    public function show(PermissionAssignmentRequest $request): JsonResponse
    {
        $this->checkTokenRights($request);

        $permission = $this->getPermissionFromModel($request, $request->permission);

        return response()->json($permission->hideSubData());
    }

    /**
     * It is not possible to modify an assignement.
     *
     * @param  PermissionAssignmentRequest $request
     * @return void
     */
    public function update(PermissionAssignmentRequest $request)
    {
        abort(405, 'Impossible de modifier l\'assignation d\'un permission');
    }

    /**
     * Restraints a permission.
     *
     * @param  PermissionAssignmentRequest $request
     * @return void
     */
    public function destroy(PermissionAssignmentRequest $request)
    {
        $this->checkTokenRights($request, 'remove');

        $permission = $this->getPermissionFromModel($request, $request->permission, 'remove');

        \Auth::user()->removePermissions($permission->id, [
            'user_id' => (\Auth::id() ?? $request->input('user_id')),
            'semester_id' => $permission->pivot->semester_id,
        ], \Auth::id(), \Scopes::isClientToken($request));
    }
}

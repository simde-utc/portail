<?php
/**
 * Gère les permissions assignées.
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
use App\Models\Semester;
use App\Http\Requests\PermissionAssignmentRequest;
use App\Models\Visibility;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasPermissions;

class AssignmentController extends Controller
{
    use HasPermissions;

    /**
     * Nécessité de pouvoir gérer les permissions assignées.
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
     * Liste les permissions assignées.
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
     * Assigne une permission.
     *
     * @param  PermissionAssignmentRequest $request
     * @return JsonResponse
     */
    public function store(PermissionAssignmentRequest $request): JsonResponse
    {
        $this->checkTokenRights($request, 'create');

        $semester_id = Semester::getSemester($request->input('semester_id'))->id;

        $request->resource->assignPermissions($request->input('permission_id'), [
            'user_id' => (\Auth::id() ?? $request->input('user_id')),
            'validated_by_id' => (\Auth::id() ?? $request->input('validated_by_id')),
            'semester_id' => $semester_id
        ], \Scopes::isClientToken($request));

        $permission = $this->getPermissionFromModel($request, $request->input('permission_id'));

        return response()->json($permission->hideSubData());
    }

    /**
     * Montre une permission assignée.
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
     * Il n'est pas possible de modifier une assignation.
     *
     * @param  PermissionAssignmentRequest $request
     * @return void
     */
    public function update(PermissionAssignmentRequest $request)
    {
        abort(405, 'Impossible de modifier l\'assignation d\'un permission');
    }

    /**
     * Retraint d'une permission.
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

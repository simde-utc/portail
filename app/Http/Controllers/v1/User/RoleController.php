<?php
/**
 * Manage user's roles.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRoleRequest;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\Visibility;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\{
    HasUsers, HasRoles, HasUserBulkMethods
};

class RoleController extends Controller
{
    use HasUserBulkMethods, HasUsers, HasRoles;

    /**
     * Must be able to see and manage user roles.
     */
    public function __construct()
    {
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-roles-users-assigned', 'client-get-roles-users-assigned'),
                \Scopes::matchOneOfDeepestChildren('user-get-roles-users-owned', 'client-get-roles-users-owned')
            ),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-create-roles-users-assigned', 'client-create-roles-users-assigned'),
                \Scopes::matchOneOfDeepestChildren('user-get-roles-users-owned', 'client-get-roles-users-owned')
            ),
            ['only' => ['store']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-edit-roles-users-assigned', 'client-edit-roles-users-assigned'),
                \Scopes::matchOneOfDeepestChildren('user-get-roles-users-owned', 'client-get-roles-users-owned')
            ),
            ['only' => ['update']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-remove-roles-users-assigned', 'client-remove-roles-users-assigned'),
                \Scopes::matchOneOfDeepestChildren('user-get-roles-users-owned', 'client-get-roles-users-owned')
            ),
            ['only' => ['destroy']]
        );
        // Can index, show, add, remove and edit roles for multiple users in a raw.
        $this->middleware(
            \Scopes::matchAnyClient(),
            ['only' => ['bulkIndex', 'bulkStore', 'bulkShow', 'bulkUpdate', 'bulkDestroy']]
        );
    }

    /**
     * List user roles.
     *
     * @param Request     $request
     * @param string|null $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id, true);
        $semester_id = $this->getSemester($request->input('semester_id'))->id;
        $roles = $user->getUserRoles(null, $semester_id);

        return response()->json($roles->map(function ($role) {
            return $role->hideData();
        }), 200);
    }

    /**
     * Add a user role.
     *
     * @param  UserRoleRequest $request
     * @param  string|null     $user_id
     * @return JsonResponse
     * @throws PortailException If the user already owns the role and we don't have the right to assign it to him.
     */
    public function store(UserRoleRequest $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id, true);

        $user->assignRoles($request->input('role_id'), [
            'validated_by_id' => (\Auth::id() ?? $request->input('validated_by_id')),
            'semester_id' => $this->getSemester($request->input('semester_id'))->id
        ], \Scopes::isClientToken($request));
        $role = $this->getRoleFromUser($request, $user, $request->input('role_id'));

        return response()->json($role->hideSubData());
    }

    /**
     * Show a user role.
     *
     * @param Request     $request
     * @param string      $user_id
     * @param string|null $role_id
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $role_id=null): JsonResponse
    {
        if (is_null($role_id)) {
            list($user_id, $role_id) = [$role_id, $user_id];
        }

        $user = $this->getUser($request, $user_id, true);
        $role = $this->getRoleFromUser($request, $user, $role_id);

        return response()->json($role->hideSubData());
    }

    /**
     * It is not possible to update a user role.
     *
     * @param  Request     $request
     * @param string      $user_id
     * @param string|null $role_id
     * @return void
     */
    public function update(Request $request, string $user_id, string $role_id=null)
    {
        abort(405, 'Impossible de modifier l\'assignation d\'un rôle');
    }

    /**
     * Delete a user role.
     *
     * @param Request     $request
     * @param string      $user_id
     * @param string|null $role_id
     * @return void
     * @throws PortailException If the user doesn't own the role and we don't have the right to assign it to him.
     */
    public function destroy(Request $request, string $user_id, string $role_id=null)
    {
        if (is_null($role_id)) {
            list($user_id, $role_id) = [$role_id, $user_id];
        }

        $user = $this->getUser($request, $user_id, true);
        $role = $this->getRoleFromUser($request, $user, $role_id);

        $user->removeRoles($role_id, [
            'semester_id' => $role->pivot->semester_id,
        ], \Auth::id(), \Scopes::isClientToken($request));
    }
}

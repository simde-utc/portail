<?php
/**
 * Gère les roles de l'utilisateur.
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
use App\Traits\Controller\v1\HasUsers;
use App\Traits\Controller\v1\HasRoles;

class RoleController extends Controller
{
    use HasUsers, HasRoles;

    /**
     * Nécessite de pouvoir voir les rôles utilisateur et gérer les rôles de l'utilisateur.
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
    }

    /**
     * Liste les rôles de l'utilisateur.
     *
     * @param Request     $request
     * @param string|null $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id, true);
        if ($request->has('semester')) {
            $semester_id = Semester::getSemester($request->input('semester'))->id;
        } else {
            $semester_id = Semester::getThisSemester()->id;
        }

        $roles = $user->getUserRoles(null, $semester_id);

        return response()->json($roles->map(function ($role) {
            return $role->hideData();
        }), 200);
    }

    /**
     * Ajoute un rôle pour l'utilisateur.
     *
     * @param  UserRoleRequest $request
     * @param  string|null     $user_id
     * @return JsonResponse
     * @throws PortailException Si l'utilisateur possède déjà un rôle on qu'on a pas le droit de le lui assigner.
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
     * Montre un rôle de l'utilisateur.
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
     * Il est impossible de mettre à jour un rôle d'un utilisateur.
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
     * Supprime un rôle de l'utlisateur
     *
     * @param Request     $request
     * @param string      $user_id
     * @param string|null $role_id
     * @return void
     * @throws PortailException Si l'utilisateur ne possède pas le rôle on qu'on a pas le droit de le lui supprimer.
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

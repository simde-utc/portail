<?php
/**
 * Manage association members.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Asso;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Http\Requests\AssoMemberRequest;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasAssos;

class MemberController extends Controller
{
    use HasAssos;

    /**
     * Must be able to see assocations and to manage members.
     * The user must be from the CAS or BDE contributor.
     */
    public function __construct()
    {
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-get-assos', 'client-get-assos'),
		        \Scopes::matchOneOfDeepestChildren('user-get-assos-members', 'client-get-assos-members'),
		        \Scopes::matchOneOfDeepestChildren('user-get-roles-assos-assigned', 'client-get-assos-members-followed'),
		        ['user:cas,contributorBde']
	        ),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-get-assos', 'client-get-assos'),
		        \Scopes::matchOneOfDeepestChildren('user-create-assos-members', 'client-create-assos-members'),
		        \Scopes::matchOneOfDeepestChildren('user-create-roles-assos-assigned', 'client-create-assos-members-followed'),
		        ['user:contributorBde']
	        ),
	        ['only' => ['create']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-get-assos', 'client-get-assos'),
		        \Scopes::matchOneOfDeepestChildren('user-edit-assos-members', 'client-edit-assos-members'),
		        \Scopes::matchOneOfDeepestChildren('user-edit-roles-assos-assigned', 'client-edit-assos-members-followed'),
		        ['user:contributorBde']
	        ),
	        ['only' => ['edit']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-get-assos', 'client-get-assos'),
		        \Scopes::matchOneOfDeepestChildren('user-remove-assos-members', 'client-remove-assos-members'),
		        \Scopes::matchOneOfDeepestChildren('user-remove-roles-assos-assigned', 'client-remove-assos-members-followed'),
		        ['user:contributorBde']
	        ),
	        ['only' => ['remove']]
        );
    }

    /**
     * Add automatically permissions and role depending on the member.
     *
     * @param Asso $asso
     * @param User $member
     * @return void
     */
    protected function addRolesAndPermissions(Asso $asso, User $member)
    {
        // Here the rights and permissions of the user are auto-affected.
        if ($member->pivot->validated_by_id) {
            $role = Role::find($member->pivot->role_id, $asso);

            $roles = (array) config('portail.roles.assos.'.($asso->login).'.'.($role->type), []);

            if (count($roles)) {
                try {
                    $member->assignRoles($roles, [
                        'semester_id' => $member->pivot->semester_id,
                        'validated_by_id' => $member->id,
                    ], true);
                } catch (\Exception $e) {
                    // Ingnoring error.
                }
            }

            $permissions = (array) config('portail.permissions.assos.'.($asso->login).'.'.($role->type), []);

            if (count($permissions)) {
                try {
                    $member->assignPermissions($permissions, [
                        'semester_id' => $member->pivot->semester_id,
                        'validated_by_id' => $member->id,
                    ], true);
                } catch (\Exception $e) {
                    // Ingnoring error.
                }
            }
        }
    }

    /**
     * List all association's members.
     *
     * @param Request $request
     * @param string  $asso_id
     * @return JsonResponse
     */
    public function index(Request $request, string $asso_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $asso = $this->getAsso($request, $asso_id);

        $members = $asso->allMembers()
            ->where('semester_id', $semester->id)
            ->whereNotNull('role_id')
            ->getSelection(true)
            ->map(function ($member) {
                $member->pivot = [
                    'role_id' => $member->role_id,
                    'validated_by_id' => $member->validated_by_id,
                    'semester_id' => $member->semester_id,
                ];

                return $member->hideData();
            });

        return response()->json($members, 200);
    }

    /**
     * Add a member to a given association.
     *
     * @param AssoMemberRequest $request
     * @param string            $asso_id
     * @return JsonResponse
     */
    public function store(AssoMemberRequest $request, string $asso_id): JsonResponse
    {
        $user = $this->getUser($request, (\Auth::id() ?? $request->input('user_id')));
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices, 'create');
        $asso = $this->getAsso($request, $asso_id);
        $scopeHead = \Scopes::getTokenType($request);

        $asso->assignMembers(\Auth::id(), [
            'role_id' => $request->input('role_id'),
            'semester_id' => $semester->id,
        ], true);

        $member = $this->getUserFromAsso($request, $asso, $user->id, $semester);

        return response()->json($member->hideData(), 201);
    }

    /**
     * Show an association member.
     *
     * @param Request $request
     * @param string  $asso_id
     * @param string  $member_id
     * @return JsonResponse
     */
    public function show(Request $request, string $asso_id, string $member_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $asso = $this->getAsso($request, $asso_id);
        $user = $this->getUserFromAsso($request, $asso, $member_id, $semester);

        return response()->json($user->hideData(), 200);
    }

    /**
     * Update an association member.
     *
     * @param AssoMemberRequest $request
     * @param string            $asso_id
     * @param string            $member_id
     * @return JsonResponse
     */
    public function update(AssoMemberRequest $request, string $asso_id, string $member_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $asso = $this->getAsso($request, $asso_id);
        $user = $this->getUserFromAsso($request, $asso, $member_id, $semester);
        $scopeHead = \Scopes::getTokenType($request);
        $requiredScopes = [
            $scopeHead.'-create-assos-members-joining-now',
            $scopeHead.'-create-assos-members-joined-now'
        ];

        if (!\Scopes::hasAll($request, $requiredScopes)) {
            abort(403, 'Vous n\'être pas autorisé à confirmer un rôle');
        }

        $forceUpdate = (
	        Role::getRole(config('portail.roles.admin.assos'), $asso)->id === $user->pivot->role_id
	        && ($lastUser = $asso->getLastUserWithRole(config('portail.roles.admin.assos')))
	        && $lastUser->id === \Auth::id()
        );

        $asso->updateMembers($member_id, [
            'role_id' => $user->pivot->role_id,
            'semester_id' => $user->pivot->semester_id,
        ], [
            'validated_by_id' => \Auth::id(),
        ], $forceUpdate);
        // If the role we want to validate can be validted by inheritance.
        $member = $this->getUserFromAsso($request, $asso, $member_id, $semester);

        $this->addRolesAndPermissions($asso, $member);

        return response()->json($member->hideSubData());
    }

    /**
     * Remove an association member.
     *
     * @param Request $request
     * @param string  $asso_id
     * @param string  $member_id
     * @return void
     */
    public function destroy(Request $request, string $asso_id, string $member_id): void
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices, 'remove');
        $asso = $this->getAsso($request, $asso_id);
        $user = $this->getUserFromAsso($request, $asso, $member_id, $semester);

        if ($asso->removeMembers($user, [
            'role_id' => $user->pivot->role_id,
            'semester_id' => $user->pivot->semester_id,
        ], \Auth::id(), $user->id === \Auth::id())) {
            abort(204);
        } else {
            abort(500, 'Impossible de retirer la personne de l\'association');
        }
    }
}

<?php

namespace App\Http\Controllers\v1\Asso;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasAssos;

class MemberController extends Controller
{
	use HasAssos;

	public function __construct() {
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-get-assos-members', 'client-get-assos-members'), // Pouvoir voir les assos membres
				\Scopes::matchOneOfDeepestChildren('user-get-roles-assos-assigned', 'client-get-assos-members-followed'),
				['user:cas,contributerBde']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-create-assos-members', 'client-create-assos-members'), // Pouvoir créé des assos membres
				\Scopes::matchOneOfDeepestChildren('user-create-roles-assos-assigned', 'client-create-assos-members-followed'),
				['user:cas,contributerBde']
			),
			['only' => ['store']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-edit-assos-members', 'client-edit-assos-members'), // Pouvoir modifier les assos membres
				\Scopes::matchOneOfDeepestChildren('user-edit-roles-assos-assigned', 'client-edit-assos-members-followed'),
				['user:cas,contributerBde']
			),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-get-assos', 'client-get-assos'), // Pouvoir voir les assos
				\Scopes::matchOneOfDeepestChildren('user-remove-assos-members', 'client-remove-assos-members'), // Pouvoir retirer les assos membres
				\Scopes::matchOneOfDeepestChildren('user-remove-roles-assos-assigned', 'client-remove-assos-members-followed'),
				['user:cas,contributerBde']
			),
			['only' => ['destroy']]
		);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @return JsonResponse
	 */
	public function index(Request $request, string $asso_id): JsonResponse {
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices);
		$asso = $this->getAsso($request, $asso_id);

		$members = $asso->allMembers()->where('semester_id', $semester->id)->whereNotNull('role_id')->getSelection(true)->map(function ($member) {
			$member->pivot = [
				'role_id' => $member->role_id,
				'validated_by' => $member->validated_by,
				'semester_id' => $member->semester_id,
			];

			return $member->hideData();
		});

		return response()->json($members, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request $request
	 * @param $asso_id
	 * @return JsonResponse
	 */
	public function store(Request $request, string $asso_id): JsonResponse {
		$user = $this->getUser($request, \Auth::id() ?? $request->input('user_id'));
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices, 'create');
		$asso = $this->getAsso($request, $asso_id);
		$scopeHead = \Scopes::getTokenType($request);

		$asso->assignMembers(\Auth::id(), [
			'role_id' => $request->input('role_id'),
			'semester_id' => $semester->id
		]);

		$member = $this->getUserFromAsso($request, $asso, $user->id, $semester);

		return response()->json($member->hideData(), 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function show(Request $request, string $asso_id, string $member_id): JsonResponse {
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices);
		$asso = $this->getAsso($request, $asso_id);
		$user = $this->getUserFromAsso($request, $asso, $member_id, $semester);

		return response()->json($user->hideData(), 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function update(Request $request, string $asso_id, string $member_id): JsonResponse {
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices);
		$asso = $this->getAsso($request, $asso_id);
		$user = $this->getUserFromAsso($request, $asso, $member_id, $semester);
		$scopeHead = \Scopes::getTokenType($request);

		if (!\Scopes::hasAll($request, [$scopeHead.'-create-assos-members-joining-now', $scopeHead.'-create-assos-members-joined-now']))
			abort(403, 'Vous n\'être pas autorisé à confirmer un rôle');

		$asso->updateMembers($member_id, [
			'semester_id' => $user->pivot->semester_id,
		], [
      'validated_by' => \Auth::id(),
    ], Role::getRole(config('portail.roles.admin.assos'))->id === $user->pivot->role_id
			&& $asso->getLastUserWithRole(config('portail.roles.admin.assos'))->id === \Auth::id());
		// Si le rôle qu'on veut valider est un rôle qui peut-être validé par héridité

		return response()->json($this->getUserFromAsso($request, $asso, $member_id, $semester)->hideSubData());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param $asso_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function destroy(Request $request, string $asso_id, string $member_id): JsonResponse {
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices, 'remove');
		$asso = $this->getAsso($request, $asso_id);
		$user = $this->getUserFromAsso($request, $asso, $member_id, $semester);

		if ($asso->removeMembers($user, [
			'semester_id' => $user->pivot->semester_id,
		], \Auth::id()))
			abort(204);
		else
			abort(500, 'Impossible de retirer la personne de l\'association');
	}
}

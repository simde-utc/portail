<?php

namespace App\Http\Controllers\v1\Group;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Services\Visible\Visible;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasGroups;

class MemberController extends Controller
{
	use HasGroups;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-groups', 'client-get-groups'),
			['only' => ['index', 'show']]);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-manage-groups', 'client-manage-groups'),
			['only' => ['store', 'update', 'destroy']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @param int $group_id
	 * @return JsonResponse
	 */
	public function index(Request $request, int $group_id): JsonResponse {
		return response()->json($this->getGroup($request, $group_id)->currentAllMembers->map(function ($member) {
			return $member->hideData();
		}));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param int $group_id
	 * @return JsonResponse
	 */
	public function store(Request $request, int $group_id): JsonResponse {
		$group = $this->getGroup($request, $group_id);

		$data = [
			'semester_id' => $request->input('semester_id', 0),
			'role_id'     => $request->input('role_id', null),
		];
		// TODO: Envoyer un mail d'invitation dans le groupe

		try {
			$group->assignMembers($request->input('member_ids', []), $data);
		} catch (PortailException $e) {
			return response()->json(["message" => $e->getMessage()], 400);
		}

		$members = $group->currentAllMembers->map(function ($member) {
			return $member->hideData();
		});

		return response()->json($members);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Request $request
	 * @param int $group_id
	 * @param int $member_id
	 * @return JsonResponse
	 */
	public function show(Request $request, int $group_id, int $member_id): JsonResponse {
		$group = $this->getGroup($request, $group_id);
		$member = $group->currentAllMembers()->where('id', $member_id)->first();

		if ($member)
			return response()->json($member->hideData());
		else
			abort(404, 'Cette personne ne fait pas partie du groupe');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param int $group_id
	 * @param int $member_id
	 * @return JsonResponse
	 */
	public function update(Request $request, int $group_id, int $member_id): JsonResponse {
		$group = $this->getGroup($request, $group_id);
		$member = $group->currentAllMembers->where('id', $member_id)->first();

		if ($member) {
			if ($member_id === \Auth::id())
				$data = [
					'semester_id'  => $request->input('semester_id', $member->pivot->semester_id),
					'role_id'      => $request->input('role_id', $member->pivot->role_id),
					'validated_by' => $member_id,
				];
			else {
				$data = [
					'semester_id' => $request->input('semester_id', $member->pivot->semester_id),
					'role_id'     => $request->input('role_id', $member->pivot->role_id),
				];
				// TODO: Envoyer un mail d'invitation dans le groupe
			}

			try {
				$group->updateMembers($member_id, [
					'semester_id' => $member->pivot->semester_id,
				], $data);
			} catch (PortailException $e) {
				return response()->json(["message" => $e->getMessage()], 400);
			}

			return response()->json($group->currentAllMembers()->where('user_id', $member_id)->first()->hideData());
		}
		else
			abort(404, 'Cette personne ne fait pas partie du groupe');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Request $request
	 * @param int $group_id
	 * @param int $member_id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function destroy(Request $request, int $group_id, int $member_id): JsonResponse {
		$group = $this->getGroup($request, $group_id);
		$member = $group->currentAllMembers->where('id', $member_id)->first();

		if ($member) {
			$data = [
				'semester_id' => $request->input('semester_id', 0),
			];

			$group->removeMembers($member_id, $data, \Auth::id());

			$members = $group->currentAllMembers->map(function ($member) {
				return $member->hideData();
			});

			return response()->json($members);
		}
		else
			abort(404, 'Cette personne ne faisait déjà pas partie du groupe');
	}
}

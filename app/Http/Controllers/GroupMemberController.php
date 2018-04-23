<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Group;
use App\Http\Requests\GroupRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;
use App\Exceptions\PortailException;

class GroupMemberController extends Controller
{
    public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-groups-enabled', 'user-get-groups-disabled'],
				['client-get-groups-enabled', 'client-get-groups-disabled']
			),
			['only' => ['index', 'show']]);
        $this->middleware(
			\Scopes::matchOne(
				['user-manage-groups']
			),
			['only' => ['store', 'update', 'destroy']]);
    }

	protected function getGroup(Request $request, int $group_id) {
		$group = Group::find($group_id);

		if ($group) {
			if ($group->user_id === \Auth::user()->id)
				return $group;
			else if ($group->hasOneMember(\Auth::user()->id)) {
				if ($group->visibility_id <= Visibility::findByType('private')->id)
					return $group;
			}
		}

		abort(404, "Groupe non trouvé");
	}

	protected function hideUsersData(Request $request, $users, bool $hidePivot = false) {
		return parent::hideUsersData($request, $users, $hidePivot);
	}

	protected function hideUserData(Request $request, $user, bool $hidePivot = false) {
		return parent::hideUserData($request, $user, $hidePivot);
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $group_id)
    {
		return response()->json($this->hideUsersData($request, $this->getGroup($request, $group_id)->currentMembersAndJoiners));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $group_id)
    {
		$group = $this->getGroup($request, $group_id);

		if ($group->visibility_id >= Visibility::findByType('owner')->id)
			$data = [
				'semester_id' => $request->input('semester_id', 0),
				'role_id' => $request->input('role_id', null),
				'validated_by' => \Auth::user()->id,
			];
		else {
			$data = [
				'semester_id' => $request->input('semester_id', 0),
				'role_id' => $request->input('role_id', null),
			];
			// TODO: Envoyer un mail d'invitation dans le groupe
		}

		try {
			$group->assignMembers($request->input('member_ids', []), $data);
		} catch (PortailException $e) {
			return response()->json(["message" => $e->getMessage()], 400);
		}

		return response()->json($this->hideUsersData($request, $group->currentMembersAndJoiners));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $group_id, int $member_id)
    {
		$group = $this->getGroup($request, $group_id);
		$member = $group->currentMembersAndJoiners->where('id', $member_id)->first();

		if ($member)
			return response()->json($this->hideUserData($request, $member));
		else
			abort(404, 'Cette personne ne fait pas partie du groupe');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $group_id, int $member_id)
    {
		$group = $this->getGroup($request, $group_id);
		$member = $group->currentMembersAndJoiners->where('id', $member_id)->first();

		if ($member) {
			if ($member_id === \Auth::user()->id)
				$data = [
					'semester_id' => $request->input('semester_id', $member->pivot->semester_id),
					'role_id' => $request->input('role_id', $member->pivot->role_id),
					'validated_by' => $member_id,
				];
			else {
				$data = [
					'semester_id' => $request->input('semester_id', $member->pivot->semester_id),
					'role_id' => $request->input('role_id', $member->pivot->role_id),
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

			return response()->json($this->hideUserData($request, $group->currentMembersAndJoiners()->where('user_id', $member_id)->first()));
		}
		else
			abort(404, 'Cette personne ne fait pas partie du groupe');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $group_id, int $member_id)
    {
		$group = $this->getGroup($request, $group_id);
		$member = $group->currentMembersAndJoiners->where('id', $member_id)->first();

		if ($member) {
			$data = [
				'semester_id' => $request->input('semester_id', 0),
			];

			try {
				$group->removeMembers($member_id, $data, \Auth::user()->id);
			} catch (PortailException $e) {
				return response()->json(["message" => $e->getMessage()], 400);
			}

			return response()->json($this->hideUserData($request, $this->getGroup($request, $group_id)->currentMembersAndJoiners));
		}
		else
			abort(404, 'Cette personne ne faisait déjà pas partie du groupe');
    }
}

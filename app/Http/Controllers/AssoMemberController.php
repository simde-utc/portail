<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Asso;
use App\Models\Semester;
use App\Http\Requests\AssoRequest;
use App\Services\Visible\Visible;
use App\Models\Visibility;
use App\Exceptions\PortailException;

class AssoMemberController extends Controller
{
    public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-assos-joined-now', 'user-get-assos-followed-now']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-create-assos']
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-set-assos']
			),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-manage-assos']
			),
			['only' => ['destroy']]
		);
    }

	protected function getAsso(Request $request, int $asso_id) {
		$asso = Asso::find($asso_id);

		if ($asso)
			// TODO : Peut être vérifé un niveau de visbilité
			return $asso;
		else
			abort(404, "Assocation non trouvée");
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
    public function index(Request $request, int $asso_id) {
		$asso = $this->getAsso($request, $asso_id);
		$choices = $this->getChoices($request, ['members', 'joiners', 'followers']);
		$semester = Semester::getSemester($request->input('semester')) ?? Semester::getThisSemester();
		$members = collect();

		if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
			throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

		if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('members', $choices))
			$members = $members->merge($asso->members()->where('semester_id', $semester->id)->get());

		if (\Scopes::has($request, 'user-get-assos-joining-now') && in_array('joiners', $choices))
			$members = $members->merge($asso->joiners()->where('semester_id', $semester->id)->get());

		if (\Scopes::has($request, 'user-get-assos-followed-now') && in_array('followers', $choices))
			$members = $members->merge($asso->followers()->where('semester_id', $semester->id)->get());

		return response()->json($this->hideUsersData($request, $members), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $asso_id) {
		$asso = $this->getAsso($request, $asso_id);

		if (\Scopes::isUserToken($request)) {
			if ($request->input('role_id')) {
				$asso->assignMembers(\Auth::id(), [
					'role_id' => $request->input('role_id'),
				]);
			}
			else {
				$asso->assignMembers(\Auth::id(), [
					'validated_by' => \Auth::id(),
				]);
			}

			$member = $asso->allMembers()->wherePivot('user_id', \Auth::id())->first();
		}

		return response()->json($this->hideUserData($request, $member));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $asso_id, int $member_id)
    {
		$asso = $this->getAsso($request, $asso_id);
		$member = $asso->currentAllMembers()->where('id', $member_id)->first();

		if ($member)
			return response()->json($this->hideUserData($request, $member));
		else
			abort(404, 'Cette personne ne fait pas partie de l\'association');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $asso_id, int $member_id)
    {
		$asso = $this->getAsso($request, $asso_id);
		$member = $asso->currentAllMembers()->where('id', $member_id)->first();

		if ($member) {
			if ($member_id === \Auth::id())
				$data = [
					'role_id' => $request->input('role_id', $member->pivot->role_id),
					'validated_by' => $member_id,
				];
			else {
				$data = [
					'role_id' => $request->input('role_id', $member->pivot->role_id),
					'validated_by' => $member_id,
				];
			}

			$asso->updateMembers($member_id, [
				'semester_id' => $member->pivot->semester_id,
			], $data);

			return response()->json($this->hideUserData($request, $asso->currentAllMembers()->where('user_id', $member_id)->first()));
		}
		else
			abort(404, 'Cette personne ne fait pas partie de l\'association');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $asso_id, int $member_id)
    {
		$asso = $this->getAsso($request, $asso_id);
		$member = $asso->currentAllMembers()->where('id', $member_id)->first();

		if ($member) {
			$data = [
				'semester_id' => $request->input('semester_id', 0),
			];

			$asso->removeMembers($member_id, $data, \Auth::id());

			return response()->json($this->hideUserData($request, $this->getAsso($request, $asso_id)->currentMembersAndJoiners));
		}
		else
			abort(404, 'Cette personne ne faisait déjà pas partie du assoe');
    }
}

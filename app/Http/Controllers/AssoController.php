<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AssoRequest;
use App\Models\Asso;
use App\Models\Semester;
use App\Http\Controllers\Controller;
use App\Exceptions\PortailException;

/**
 * @resource Association
 *
 * Gestion des associations
 */
class AssoController extends Controller
{
	public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				['user-get-assos-joined-now', 'user-get-assos-followed-now']
			),
			['only' => ['index', 'show']]);
		$this->middleware(
			\Scopes::matchOne(
				['user-get-assos-joined-now', 'user-get-assos-followed-now']
			),
			['only' => ['store', 'update', 'destroy']]);
	}

	protected function hideAssoData(Request $request, $asso) {
		$asso->makeHidden('type_asso_id');

		if ($asso->pivot) {
			$asso->pivot->makeHidden(['user_id', 'asso_id']);

			if ($asso->pivot->semester_id === 0)
				$asso->pivot->makeHidden('semester_id');

			if (is_null($asso->pivot->role_id))
				$asso->pivot->makeHidden('role_id');

			if (is_null($asso->pivot->validated_by))
				$asso->pivot->makeHidden('validated_by');
		}

		return $asso;
	}

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (\Scopes::isUserToken($request)) {
			$assos = collect();
			$choices = $this->getChoices($request, ['joined', 'joining', 'followed']);

			if ($request->input('semester_id') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
				throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

			if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('joined', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-joined')) {
					$semester = Semester::find($request->input('semester_id'));
					$addAssos = $request->user()->joinedAssos();
					$addAssos = $addAssos->where('semester_id', $semester ? $semester->id : Semester::getThisSemester()->id);

					$assos = $assos->merge($addAssos->with('type:id,name,description')->get());
				}
				else
					$assos = $assos->merge($request->user()->currentJoinedAssos()->with('type:id,name,description')->get());
			}

			if (\Scopes::has($request, 'user-get-assos-joining-now') && in_array('joining', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-joining')) {
					$semester = Semester::find($request->input('semester_id'));
					$addAssos = $request->user()->joiningAssos();
					$addAssos = $addAssos->where('semester_id', $semester ? $semester->id : Semester::getThisSemester()->id);

					$assos = $assos->merge($addAssos->with('type:id,name,description')->get());
				}
				else
					$assos = $assos->merge($request->user()->currentJoiningAssos()->with('type:id,name,description')->get());
			}

			if (\Scopes::has($request, 'user-get-assos-followed-now') && in_array('followed', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-followed')) {
					$semester = Semester::find($request->input('semester_id'));
					$addAssos = $request->user()->followedAssos();
					$addAssos = $addAssos->where('semester_id', $semester ? $semester->id : Semester::getThisSemester()->id);

					$assos = $assos->merge($addAssos->with('type:id,name,description')->get());
				}
				else
					$assos = $assos->merge($request->user()->currentFollowedAssos()->with('type:id,name,description')->get());
			}
		}

		foreach ($assos as $asso)
			$this->hideAssoData($request, $asso);

		return response()->json($assos, 200);
	}

	/**
	 * Store Association
	 *
	 * Créer une Association
	 * @param  \Illuminate\Http\AssoRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AssoRequest $request) {

	}

	/**
	 * Show Association
	 *
	 * Retourne l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, int $id) {
		$asso = Asso::with('type:id,name,description')->find($id);

		if ($asso) {
			if (\Scopes::isUserToken($request)) {
				$choices = $this->getChoices($request, ['joined', 'joining', 'followed']);

				if ($request->input('semester_id') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-followed']))
					throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followed');

				$semester = Semester::find($request->input('semester_id'));
				$users = $asso->membersAndFollowers()->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester()->id)->wherePivot('user_id', $request->user()->id)->get();
				$userData = [];

				if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('joined', $choices)) {
					$userData['is_member'] = false;

					foreach ($users as $user) {
						if (!is_null($user->pivot->role_id) && !is_null($user->pivot->validated_by))
							$userData['is_member'] = true;
					}
				}

				if (\Scopes::has($request, 'user-get-assos-joining-now') && in_array('joining', $choices)) {
					$userData['is_joining'] = false;

					foreach ($users as $user) {
						if (!is_null($user->pivot->role_id) && is_null($user->pivot->validated_by))
							$userData['is_joining'] = true;
					}
				}

				if (\Scopes::has($request, 'user-get-assos-followed-now') && in_array('followed', $choices)) {
					$userData['is_follower'] = false;

					foreach ($users as $user) {
						if (is_null($user->pivot->role_id))
							$userData['is_follower'] = true;
					}
				}

				$asso->user = $userData;
			}

			return response()->json($this->hideAssoData($request, $asso), 200);
		}
		else
			return response()->json(['message' => 'L\'asso demandée n\'a pas pu être trouvée'], 404);
	}

	/**
	 * Update Association
	 *
	 * Met à jour l'association si elle existe
	 * @param  \Illuminate\Http\AssoRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(AssoRequest $request, $id) {
		$asso = Asso::find($id);

		if ($asso) {
			if ($asso->update($request->input()))
				return response()->json($asso, 201);
			else
				return response()->json(['message' => 'L\'association n\'a pas pu être modifiée'], 500);
		}
		else
			return response()->json(['message' => 'L\'association demandée n\'a pas été trouvée'], 404);
	}

	/**
	 * Delete Association
	 *
	 * Supprime l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		$asso = Asso::find($id);

		if ($asso) {
			if ($asso->destroy())
				return response()->json(['message' => 'L\'assocition a bien été supprimée'], 200);
			else
				return response()->json(['message' => 'L\'association n\'a pas pu être supprimée'], 500);
		}
		else
			return response()->json(['message' => 'L\'association demandée n\'a pas pu être trouvée'], 404);
	}
}

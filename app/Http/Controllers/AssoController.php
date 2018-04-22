<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AssoRequest;
use App\Models\Asso;
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

	protected function hideAssoData($asso) {
		$asso->makeHidden('type_asso_id');
		$asso->pivot->makeHidden(['user_id', 'asso_id']);

		if ($asso->pivot->semester_id === 0)
			$asso->pivot->makeHidden('semester_id');

		if (is_null($asso->pivot->role_id))
			$asso->pivot->makeHidden('role_id');

		if (is_null($asso->pivot->validated_by))
			$asso->pivot->makeHidden('validated_by');
	}

	protected function hideMemberData(Request $request, $members) {
		$toHide = [];

		if (!\Scopes::has($request, 'user-get-info-identity-emails-main'))
			array_push($toHide, 'email');

		if (!\Scopes::has($request, 'user-get-info-identity-timestamps'))
			array_push($toHide, 'last_login_at', 'created_at', 'updated_at');

		$members->each(function ($member) use ($toHide) {
			$member->makeHidden($toHide);
			$member->pivot->makeHidden(['group_id', 'user_id']);

			if ($member->pivot->semester_id === 0)
				$member->pivot->makeHidden('semester_id');

			if (is_null($member->pivot->role_id))
				$member->pivot->makeHidden('role_id');

			if (is_null($member->pivot->validated_by))
				$member->pivot->makeHidden('validated_by');
		});

		return $members;
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

			if ($request->input('semester_id') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-followed']))
				throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes associés');

			if (\Scopes::has($request, 'user-get-assos-joined-now') && (in_array('joined', $choices) || in_array('joining', $choices))) {
				if (in_array('joined', $choices) && in_array('joining', $choices))
					$assos = $request->user()->assos()->with('type:id,name,description');
				if (in_array('joined', $choices))
					$assos = $request->user()->joinedAssos()->with('type:id,name,description');
				else
					$assos = $request->user()->joiningAssos()->with('type:id,name,description');

				if (\Scopes::has($request, 'user-get-assos-joined')) {
					$semester = Semester::find($request->input('semester_id'));
					$assos = $semester ? $assos->where('semester_id', $semester->id) : $assos;

					$assos = $assos->get();
				}
				else if ($request->input('semester_id') === null)
					$assos = $assos->get();
			}

			if (\Scopes::has($request, 'user-get-assos-followed-now') && in_array('followed', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-followed')) {
					$semester = Semester::find($request->input('semester_id'));
					$addAssos = $request->user()->followedAssos()->with('type:id,name,description');
					$addAssos = $semester ? $addAssos->where('semester_id', $semester->id) : $addAssos;

					$assos = $assos->merge($assos->get());
				}
				else if ($request->input('semester_id') === null)
					$assos = $assos->merge($request->user()->currentFollowedAssos()->with('type:id,name,description')->get());
			}

		}

		foreach ($assos as $asso)
			$this->hideAssoData($asso);

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
		$asso = Asso::create($request->input());

		if ($asso)
			return response()->json($asso, 201);
		else
			return response()->json(['message' => 'Impossible de créer l\'association'], 500);
	}

	/**
	 * Show Association
	 *
	 * Retourne l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		$asso = Asso::find($id);

		if ($asso)
			return response()->json($asso, 200);
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

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

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		if (\Scopes::isUserToken($request)) {
			$assos = collect();

			if ($request->input('semester_id') && !\Scopes::hasOne($request,  ['user-get-assos-joined', 'user-get-assos-followed']))
				throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes associés');

			if (\Scopes::has($request, 'user-get-assos-joined-now')) {
				if (\Scopes::has($request, 'user-get-assos-joined')) {
					$semester = Semester::find($request->input('semester_id'));
					$assos = $request->user()->joinedAssos();
					$assos = $semester ? $assos->where('semester_id', $semester->id) : $assos;

					$assos = $assos->get();
				}
				else if ($request->input('semester_id') === null)
					$assos = $request->user()->currentJoinedAssos;
			}

			if (\Scopes::has($request, 'user-get-assos-followed-now')) {
				if (\Scopes::has($request, 'user-get-assos-followed')) {
					$semester = Semester::find($request->input('semester_id'));
					$addAssos = $request->user()->followedAssos();
					$addAssos = $semester ? $addAssos->where('semester_id', $semester->id) : $addAssos;

					$assos = $assos->merge($assos->get());
				}
				else if ($request->input('semester_id') === null)
					$assos = $assos->merge($request->user()->currentFollowedAssos);
			}

		}

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AssoRequest;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
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
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne(
					['user-create-assos']
				), [
					'admin',
				]
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
			array_merge(
				\Scopes::matchOne(
					['user-remove-assos']
				), [
					'admin',
				]
			),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations
	 * @return \Illuminate\Http\Response
	 */
	public function index(AssoRequest $request) {
		if (isset($request['stage']) || isset($request['fromStage'])) {
			$assos = isset($request['stage']) ? Asso::getStage($request->stage, $request->type_asso_id) : Asso::getFromStage($request->fromStage, $request->type_asso_id);
		}
		else if (\Scopes::isClientToken($request) || isset($request['all'])) {
			$assos = Asso::with('type:id,name,description')->get();
		}
		else {
			$assos = collect();
			$choices = $this->getChoices($request, ['joined', 'joining', 'followed']);
			$semester = Semester::getSemester($request->input('semester')) ?? Semester::getThisSemester();

			if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-joining', 'user-get-assos-followed']))
				throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followeds');

			if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('joined', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-joined')) {
					$addAssos = $request->user()->joinedAssos();
					$addAssos = $addAssos->where('semester_id', $semester->id);

					$assos = $assos->merge($addAssos->with('type:id,name,description')->get());
				}
				else
					$assos = $assos->merge($request->user()->currentJoinedAssos()->with('type:id,name,description')->get());
			}

			if (\Scopes::has($request, 'user-get-assos-joining-now') && in_array('joining', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-joining')) {
					$addAssos = $request->user()->joiningAssos();
					$addAssos = $addAssos->where('semester_id', $semester->id);

					$assos = $assos->merge($addAssos->with('type:id,name,description')->get());
				}
				else
					$assos = $assos->merge($request->user()->currentJoiningAssos()->with('type:id,name,description')->get());
			}

			if (\Scopes::has($request, 'user-get-assos-followed-now') && in_array('followed', $choices)) {
				if (\Scopes::has($request, 'user-get-assos-followed')) {
					$addAssos = $request->user()->followedAssos();
					$addAssos = $addAssos->where('semester_id', $semester->id);

					$assos = $assos->merge($addAssos->with('type:id,name,description')->get());
				}
				else
					$assos = $assos->merge($request->user()->currentFollowedAssos()->with('type:id,name,description')->get());
			}
		}

		foreach ($assos as $asso)
			$asso->hideAssoData();

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

		// On vérifie la création
		if ($asso) {
			// Après la création, on ajoute son président (non confirmé évidemment)
			$asso->assignRoles(config('portail.roles.admin.assos'), [
				'user_id' => $request->input('user_id'),
			]);

			// On met l'asso en état inactif
			$asso->delete();

			// TODO: Envoyer un mail de confirmation et de demande de confirmation par les assos parents

			return response()->json($asso, 201);
		}
		else
			abort(500, 'L\'asso n\'as pas pu être créée');
	}

	/**
	 * Show Association
	 *
	 * Retourne l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, int $id) {
		$asso = Asso::with('type:id,name,description')->withTrashed()->find($id);

		if ($asso) {
			if (\Scopes::isUserToken($request)) {
				$choices = $this->getChoices($request, ['member', 'joiner', 'followed']);

				if ($request->input('semester') && !\Scopes::hasOne($request, ['user-get-assos-joined', 'user-get-assos-followed']))
					throw new PortailException('Il n\'est pas possible de définir un semestre particulier sans les scopes user-get-assos-joined ou user-get-assos-joining ou user-get-assos-followed');

				$semester = Semester::getSemester($request->input('semester'));
				$users = $asso->membersAndFollowers()->wherePivot('semester_id', $semester ? $semester->id : Semester::getThisSemester()->id)->wherePivot('user_id', $request->user()->id)->get();
				$userData = [];

				if (\Scopes::has($request, 'user-get-assos-joined-now') && in_array('member', $choices)) {
					$userData['is_member'] = false;

					foreach ($users as $user) {
						if (!is_null($user->pivot->role_id) && !is_null($user->pivot->validated_by))
							$userData['is_member'] = true;
					}
				}

				if (\Scopes::has($request, 'user-get-assos-joining-now') && in_array('joiner', $choices)) {
					$userData['is_joiner'] = false;

					foreach ($users as $user) {
						if (!is_null($user->pivot->role_id) && is_null($user->pivot->validated_by))
							$userData['is_joiner'] = true;
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

			return response()->json($asso->hideAssoData(), 200);
		}
		else
			abort(404, 'L\'asso demandée n\'a pas pu être trouvée');
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
		$asso = Asso::withTrashed()->find($id);

		if ($asso) {
			if (isset($request['validate'])) {
				$asso->updateRoles(config('portail.roles.admin.assos'), [
					'validated_by' => null,
				], [
					'validated_by' => \Auth::id(),
				], $asso->getLastUserWithRole(config('portail.roles.admin.assos'))->id === \Auth::id());
			}

			if (!$asso->hasOneRole('resp communication', ['user_id' => \Auth::id()]) && !\Auth::user()->hasOneRole('admin')) {
				if (isset($request['validate']))
					return response()->json($asso, 200);

				abort(403, 'Il est nécessaire de posséder les droits pour pouvoir modifier cette association');
			}

			if (isset($request['restore'])) {
				if (!\Auth::user()->hasOneRole('admin'))
					abort(403, 'Il est nécessaire de posséder les droits admin pour pouvoir restaurer cette association');

				$asso->restore();
			}

			if ($asso->update($request->input()))
				return response()->json($asso, 200);
			else
				abort(500, 'L\'association n\'a pas pu être modifiée');
		}
		else
			abort(404, 'L\'association demandée n\'a pas été trouvée');
	}

	/**
	 * Delete Association
	 *
	 * Supprime l'association si elle existe
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id) {
		$asso = Asso::find($id);

		if ($asso) {
			if ($asso->childs()->count() > 0)
				abort(400, 'Il n\'est pas possible de supprimer une association parente');

			if (!\Auth::user()->hasOneRole('admin'))
				abort(403, 'Il est nécessaire de posséder les droits admin pour pouvoir supprimer cette association');

			if ($asso->delete())
				abort(204);
			else
				abort(500, 'L\'association n\'a pas pu être supprimée');
		}
		else
			abort(404, 'L\'association demandée n\'a pas pu être trouvée');
	}
}

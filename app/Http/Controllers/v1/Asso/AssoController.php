<?php

namespace App\Http\Controllers\v1\Asso;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AssoRequest;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
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
			\Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOneOfDeepestChildren('user-create-assos', 'client-create-assos'), [
					'user:admin',
				]
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-set-assos', 'client-set-assos'),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOneOfDeepestChildren('user-remove-assos', 'client-remove-assos'), [
					'user:admin',
				]
			),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations
	 * @param AssoRequest $request
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function index(AssoRequest $request): JsonResponse {
		if (isset($request['stage']) || isset($request['fromStage']) || isset($request['toStage']) || isset($request['allStages'])) {
			$inputs = $request->input();
			unset($inputs['stage']);
			unset($inputs['fromStage']);
			unset($inputs['toStage']);
			unset($inputs['allStages']);

			$assos = isset($request['stage']) ? Asso::getStage($request->stage, $inputs, 'type:id,name,description') : Asso::getStages($request->fromStage, $request->toStage, $inputs, 'type:id,name,description');
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
			$asso->hideData();

		return response()->json($assos, 200);
	}

	/**
	 * Store Association
	 *
	 * Créer une Association
	 * @param AssoRequest $request
	 * @return JsonResponse
	 */
	public function store(AssoRequest $request): JsonResponse {
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
	 * @param Request $request
	 * @param  int/string $id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function show(Request $request, $id): JsonResponse {
		$asso = Asso::with(isset($request['withChildren']) ? [
			'type:id,name,description',
			'children',
		] : [
			'type:id,name,description',
		])->withTrashed();

		if (is_numeric($id))
			$asso = $asso->find($id);
		else
			$asso = $asso->where('login', $id)->first();

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

			return response()->json($asso->hide(), 200);
		}
		else
			abort(404, 'L\'asso demandée n\'a pas pu être trouvée');
	}

	/**
	 * Update Association
	 *
	 * Met à jour l'association si elle existe
	 * @param AssoRequest $request
	 * @param  int/string $id
	 * @return JsonResponse
	 */
	public function update(AssoRequest $request, $id): JsonResponse {
		$asso = Asso::withTrashed();

		if (is_numeric($id))
			$asso = $asso->find($id);
		else
			$asso = $asso->where('login', $id)->first();

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
	 * @param Request $request
	 * @param  int/string $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, $id): JsonResponse {
		if (is_numeric($id))
			$asso = Asso::find($id);
		else
			$asso = Asso::where('login', $id)->first();

		if ($asso) {
			if ($asso->children()->count() > 0)
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

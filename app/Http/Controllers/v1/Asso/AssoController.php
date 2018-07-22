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
use App\Traits\Controller\v1\HasAssos;

/**
 * @resource Association
 *
 * Gestion des associations
 */
class AssoController extends Controller
{
	use HasAssos;

	public function __construct() {
		$this->middleware(
			\Scopes::matchOne('user-get-assos', 'client-get-assos'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-create-assos', 'client-create-assos'),
				['permission:asso']
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne('user-edit-assos', 'client-edit-assos'),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-remove-assos', 'client-remove-assos'),
				['permission:asso']
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
		if ($request->filled('stage') || $request->filled('fromStage') || $request->filled('toStage') || $request->has('allStages')) {
			$toHide = function ($assos) {
				return $assos->map(function ($asso) {
					return $asso->hideData()->makeVisible('children');
				});
			};

			$assos = $request->filled('stage') ? Asso::getStage($request->stage, [], [], $toHide) : Asso::getStages($request->fromStage, $request->toStage, [], [], $toHide);
		}
		else {
			$assos = Asso::get()->map(function ($asso) {
				return $asso->hideData();
			});
		}

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

		if ($asso) {
			// Après la création, on ajoute son président (non confirmé évidemment)
			$asso->assignRoles(config('portail.roles.admin.assos'), [
				'user_id' => $request->input('user_id'),
			]);

			// On met l'asso en état inactif
			$asso->softDelete();

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
		$asso = $this->getAsso($request, $id);

		return response()->json($asso->hideSubData(), 200);
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
		$asso = $this->getAsso($request, $id);

		if (isset($request['validate'])) {
			$asso->updateRoles(config('portail.roles.admin.assos'), [
				'validated_by' => null,
			], [
				'validated_by' => \Auth::id(),
			], $asso->getLastUserWithRole(config('portail.roles.admin.assos'))->id === \Auth::id());

			return response()->json($asso, 200);
		}

		if (!$asso->hasOnePermission('asso_data', ['user_id' => \Auth::id()]) && !\Auth::user()->hasOneRole('admin'))
			abort(403, 'Il est nécessaire de posséder les droits pour pouvoir modifier cette association');

		if (isset($request['restore'])) {
			if (!\Auth::user()->hasOnePermission('asso'))
				abort(403, 'Il est nécessaire de posséder les droits associations pour pouvoir restaurer cette association');

			$asso->restore();
		}

		if ($asso->update($request->input()))
			return response()->json($asso, 200);
		else
			abort(500, 'L\'association n\'a pas pu être modifiée');
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
		$asso = $this->getAsso($request, $id);

		if ($asso->children()->exists())
			abort(400, 'Il n\'est pas possible de supprimer une association parente');

		if ($asso->softDelete())
			abort(204);
		else
			abort(500, 'L\'association n\'a pas pu être supprimée');
	}
}

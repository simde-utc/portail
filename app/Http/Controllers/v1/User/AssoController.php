<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AssoRequest;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasUsers;
use App\Traits\Controller\v1\HasAssos;

/**
 * @resource Association
 *
 * Gestion des associations
 */
class AssoController extends Controller
{
	use HasUsers, HasAssos;

	protected $initialChoices = ['joined', 'joining', 'followed'];

	public function __construct() {
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-get-assos-members', 'client-get-assos-members'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-create-assos-members', 'client-create-assos-members'),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-edit-assos-members', 'client-edit-assos-members'),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOneOfDeepestChildren('user-remove-assos-members', 'client-remove-assos-members'),
			['only' => ['destroy']]
		);
	}

	protected function getSemester(Request $request, array $choices) {
		$scopeHead = \Scopes::getTokenType($request);

		if ($request->filled('semester')) {
			if (in_array('joined', $choices) && !\Scopes::hasOne($request, $scopeHead.'-get-assos-members-joined-now'))
				throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour récupérer les associations rejoins par l\'utilisateur');

			if (in_array('joining', $choices) && !\Scopes::hasOne($request, $scopeHead.'-get-assos-members-joining-now'))
				throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour récupérer les associations que l\'utilisateur a demandé à rejoindre');

			if (in_array('followed', $choices) && !\Scopes::hasOne($request, $scopeHead.'-get-assos-members-followed-now'))
				throw new PortailException('Vous n\'avez pas les droits pour spécifier un semestre particulier pour récupérer les associations que l\'utilisateur suit');

			return Semester::getSemester($request->input('semester'));
		}

		return Semester::getThisSemester();
	}

	protected function getChoices(Request $request, array $initialChoices) {
		$scopeHead = \Scopes::getTokenType($request);
		$choices = [];

		foreach ($initialChoices as $choice) {
			if (\Scopes::hasOne($request, $scopeHead.'-get-assos-members-'.$choice.'-now'))
				$choices[] = $choice;
		}

		return parent::getChoices($request, $choices);
	}

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations de l'utilisateur
	 * @param AssoRequest $request
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function index(AssoRequest $request, int $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id);
		$choices = $this->getChoices($request, $this->initialChoices);
		$semester = $this->getSemester($request, $choices);

		$assos = collect()->merge(
			in_array('joined', $choices) ? $user->joinedAssos()->where('semester_id', $semester->id)->get() : collect(),
			in_array('joining', $choices) ? $user->joiningAssos()->where('semester_id', $semester->id)->get() : collect(),
			in_array('followed', $choices) ? $user->followedAssos()->where('semester_id', $semester->id)->get() : collect()
		)->map(function ($asso) {
			return $asso->hideData();
		});

		return response()->json($assos, 200);
	}

	/**
	 *
	 * @param AssoRequest $request
	 * @return JsonResponse
	 */
	public function store(AssoRequest $request): JsonResponse {
		abort(405);
	}

	/**
	 * Show Association
	 *
	 * Retourne l'association si elle est suivie par l'utilisateur
	 * @param Request $request
	 * @param  int/string $id
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function show(Request $request, $user_id, $id = null): JsonResponse {
		if (is_null($id))
			list($user_id, $id) = [$id, $user_id];

		$user = $this->getUser($request, $user_id);
		$choices = $this->getChoices($request, $this->initialChoices);
		$semester = $this->getSemester($request, $choices);
		$asso = $this->getAsso($request, $id, true);

		$asso = (in_array('joined', $choices) ? $user->joinedAssos()->where('semester_id', $semester->id)->where('asso_id', $asso->id)->first() : null)
			?? (in_array('joining', $choices) ? $user->joiningAssos()->where('semester_id', $semester->id)->where('asso_id', $asso->id)->first() : null)
			?? (in_array('followed', $choices) ? $user->followedAssos()->where('semester_id', $semester->id)->where('asso_id', $asso->id)->first() : null);

		if ($asso)
			return response()->json($asso->hideSubData(), 200);
		else
			abort(404, "Assocation non trouvée");
	}

	/**
	 *
	 * @param AssoRequest $request
	 * @param  int/string $id
	 * @return JsonResponse
	 */
	public function update(AssoRequest $request, $id): JsonResponse {
		abort(405);
	}

	/**
	 *
	 * @param Request $request
	 * @param  int/string $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, $id): JsonResponse {
		abort(405);
	}
}

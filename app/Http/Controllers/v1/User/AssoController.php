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

	/**
	 * List Associations
	 *
	 * Retourne la liste des associations de l'utilisateur
	 * @param AssoRequest $request
	 * @return JsonResponse
	 * @throws PortailException
	 */
	public function index(AssoRequest $request, string $user_id = null): JsonResponse {
		$user = $this->getUser($request, $user_id);
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices);

		$assos = collect()->merge(
			in_array('joined', $choices) ? $user->joinedAssos()->where('semester_id', $semester->id)->get() : collect())->merge(
			in_array('joining', $choices) ? $user->joiningAssos()->where('semester_id', $semester->id)->get() : collect())->merge(
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
	public function show(Request $request, string $user_id, string $id = null): JsonResponse {
		if (is_null($id))
			list($user_id, $id) = [$id, $user_id];

		$user = $this->getUser($request, $user_id);
		$choices = $this->getChoices($request);
		$semester = $this->getSemester($request, $choices);
		$asso = $this->getAsso($request, $id, $user, $semester);

		return response()->json($asso->hideSubData(), 200);
	}

	/**
	 *
	 * @param AssoRequest $request
	 * @param  int/string $id
	 * @return JsonResponse
	 */
	public function update(AssoRequest $request, string $user_id, string $id = null): JsonResponse {
		abort(405);
	}

	/**
	 *
	 * @param Request $request
	 * @param  int/string $id
	 * @return JsonResponse
	 */
	public function destroy(Request $request, string $user_id, string $id = null): JsonResponse {
		abort(405);
	}
}

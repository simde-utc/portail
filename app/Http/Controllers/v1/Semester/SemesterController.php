<?php

namespace App\Http\Controllers\v1\Semester;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Semester;


/**
 * @resource Semester
 *
 * Gestion des semestres
 */
class SemesterController extends Controller
{
	public function __construct() {
		$this->middleware(
			\Scopes::allowPublic()->matchAnyUserOrClient()
		);
	}

	/**
	 * List Semesters
	 * @return JsonResponse
	 */
	public function index(): JsonResponse {
		$semesters = Semester::getSelection()->map(function ($semester) {
			return $semester->hideData();
		});

		return response()->json($semesters, 200);
	}

	/**
	 * Create Semester
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(): JsonResponse {
		abort(405, 'Il n\'est pas possible de créer un semester');
	}

	/**
	 * Show Semester
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show(string $id): JsonResponse {
		$semester = Semester::getSemester($id);

		if ($semester)
			return response()->json($semester->hideSubData(), 200);
		else
			abort(404, 'Semestre non existant');
	}

	/**
	 * Update Semester
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(string $id): JsonResponse {
		abort(405, 'Il n\'est pas possible de modifier un semester');
	}

	/**
	 * Delete Semester
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(): JsonResponse {
		abort(405, 'Il n\'est pas possible de supprimer un semester');
	}
}

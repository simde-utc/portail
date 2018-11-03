<?php
/**
 * Gère les semestres.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Semester;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    /**
     * Récupération publique ou sous scopes.
     */
    public function __construct()
    {
        $this->middleware(
        \Scopes::allowPublic()->matchAnyUserOrClient()
        );
    }

    /**
     * Liste les semestres.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $semesters = Semester::getSelection()->map(function ($semester) {
            return $semester->hideData();
        });

        return response()->json($semesters, 200);
    }

    /**
     * Création non possible (génération automatique).
     *
     * @return void
     */
    public function store(): JsonResponse
    {
        abort(405, 'Il n\'est pas possible de créer un semester');
    }

    /**
     * Montre un semestre.
     *
     * @param  string $semester_id
     * @return JsonResponse
     */
    public function show(string $semester_id): JsonResponse
    {
        $semester = Semester::getSemester($semester_id);

        if ($semester) {
            return response()->json($semester->hideSubData(), 200);
        } else {
            abort(404, 'Semestre non existant');
        }
    }

    /**
     * Mise à jour non possible (génération automatique).
     *
     * @param  string $semester_id
     * @return void
     */
    public function update(string $semester_id): JsonResponse
    {
        abort(405, 'Il n\'est pas possible de modifier un semester');
    }

    /**
     * Suppression non possible (génération automatique).
     *
     * @param  string $semester_id
     * @return void
     */
    public function destroy(string $semester_id): JsonResponse
    {
        abort(405, 'Il n\'est pas possible de supprimer un semester');
    }
}

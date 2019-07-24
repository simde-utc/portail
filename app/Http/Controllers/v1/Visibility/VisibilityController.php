<?php
/**
 * Visibilities management.
 *
 * @author Josselin Pennors <josselin.pennors@etu.utc.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Visibility;

use App\Http\Controllers\v1\Controller;
use App\Http\Requests\VisibilityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Visibility;

class VisibilityController extends Controller
{
    /**
     * Readable by anyone.
     * Editable sub-scopes.
     */
    public function __construct()
    {
        // The visibility retrivement is public.
        $this->middleware(
            \Scopes::allowPublic()->matchAnyUserOrClient(),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
            \Scopes::matchOne('user-manage-portail-visibility', 'client-manage-portail-visibility'),
            ['only' => ['create', 'edit', 'remove']]
        );
    }

    /**
     * List all visibilities.
     *
     * @param  VisibilityRequest $request
     * @return JsonResponse
     */
    public function index(VisibilityRequest $request): JsonResponse
    {
        $visibilities = Visibility::getSelection()->map(function ($visibility) {
            return $visibility->hideData();
        });

        return response()->json($visibilities, 200);
    }

    /**
     * Create some visibilities.
     *
     * @param  VisibilityRequest $request
     * @return JsonResponse
     */
    public function store(VisibilityRequest $request): JsonResponse
    {
        $visibility = Visibility::create($request->all());

        return response()->json($visibility->hideSubData(), 200);
    }

    /**
     * Show a visibility.
     *
     * @param  string $visibility_id
     * @return JsonResponse
     */
    public function show(string $visibility_id): JsonResponse
    {
        $visibility = Visibility::find($visibility_id);

        if ($visibility) {
            return response()->json($visibility->hideSubData(), 200);
        } else {
            abort(404, 'Visibilité non trouvée');
        }
    }

    /**
     * Update a visibility.
     *
     * @param  VisibilityRequest $request
     * @param  string            $visibility_id
     * @return JsonResponse
     */
    public function update(VisibilityRequest $request, string $visibility_id): JsonResponse
    {
        $visibility = Visibility::find($visibility_id);

        if ($visibility) {
            if ($visibility->update($request->input())) {
                return response()->json($visibility->hideSubData(), 201);
            } else {
                abort(500, 'Impossible de modifier la visibilité');
            }
        } else {
            abort(404, 'Visibilité non trouvée');
        }
    }

    /**
     * Delete a visibility.
     *
     * @param  VisibilityRequest $request
     * @param  string            $visibility_id
     * @return void
     */
    public function destroy(VisibilityRequest $request, string $visibility_id): void
    {
        $visibility = Visibility::find($visibility_id);

        if ($visibility) {
            if ($visibility->delete()) {
                abort(204);
            } else {
                abort(500, 'Impossible de suprimer la visibilité');
            }
        } else {
            abort(404, 'Visibilité non trouvée');
        }
    }
}

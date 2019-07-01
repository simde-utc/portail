<?php
/**
 * Gère les emplacements de lieux.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Location;

use App\Http\Controllers\v1\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PlaceRequest;
use App\Traits\Controller\v1\HasPlaces;

class PlaceController extends Controller
{
    use HasPlaces;

    /**
     * Nécessité de pouvoir gérer les emplacements.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-get-locations-places'),
            ['only' => ['index', 'show', 'bulkShow']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-create-locations-places'),
            ['only' => ['store', 'bulkStore']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-set-locations-places'),
            ['only' => ['update', 'bulkUpdate']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-manage-locations-places'),
            ['only' => ['destroy', 'bulkDestroy']]
        );
    }

    /**
     * Liste les emplacements.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $places = Place::get()->map(function ($place) {
            return $place->hideData();
        });

        return response()->json($places);
    }

    /**
     * Créer un emplacement.
     *
     * @param PlaceRequest $request
     * @return JsonResponse
     */
    public function store(PlaceRequest $request): JsonResponse
    {
        $inputs = $request->input();
        $inputs['position'] = $this->getPosition($request);

        $place = Place::create($inputs);

        return response()->json($place->hideSubData());
    }

    /**
     * Montre un emplacement.
     *
     * @param Request $request
     * @param string  $place_id
     * @return JsonResponse
     */
    public function show(Request $request, string $place_id): JsonResponse
    {
        $place = $this->getPlace($request, $place_id);

        return response()->json($place->hideSubData());
    }

    /**
     * Met à jour un emplacement.
     *
     * @param PlaceRequest $request
     * @param string       $place_id
     * @return JsonResponse
     */
    public function update(PlaceRequest $request, string $place_id): JsonResponse
    {
        $place = $this->getPlace($request, $place_id);

        $inputs = $request->input();
        $inputs['position'] = $this->getPosition($request);

        if ($place->update($inputs)) {
            return response()->json($place->hideSubData(), 201);
        } else {
            abort(500, 'Impossible d\'actualiser le lieu');
        }
    }

    /**
     * Supprime un emplacement.
     *
     * @param Request $request
     * @param string  $place_id
     * @return void
     */
    public function destroy(Request $request, string $place_id): void
    {
        $place = $this->getPlace($request, $place_id);

        if ($place->delete()) {
            abort(204);
        } else {
            abort(500, 'Erreur lors de la suppression de le lieu');
        }
    }
}

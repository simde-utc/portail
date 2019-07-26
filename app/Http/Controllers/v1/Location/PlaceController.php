<?php
/**
 * Manage locations places.
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
     * Must be able to manage places.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-get-locations-places'),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-create-locations-places'),
            ['only' => ['create']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-set-locations-places'),
            ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-manage-locations-places'),
            ['only' => ['remove']]
        );
    }

    /**
     * List places.
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
     * Create a place.
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
     * Show a place.
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
     * Update a place.
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
     * Delete a place.
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

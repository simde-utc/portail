<?php
/**
 * Manages locations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Location;

use App\Http\Controllers\v1\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\LocationRequest;
use App\Traits\HasPosition;

class LocationController extends Controller
{
    use HasPosition;

    /**
     * Must be able to manage locations.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne(['client-get-locations']),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
            \Scopes::matchOne(['client-create-locations']),
            ['only' => ['create']]
        );
        $this->middleware(
            \Scopes::matchOne(['client-set-locations']),
            ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOne(['client-manage-locations']),
            ['only' => ['remove']]
        );
    }

    /**
     * Lists locations.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $locations = Location::get()->map(function ($location) {
            return $location->hideData();
        });

        return response()->json($locations);
    }

    /**
     * Creates a location.
     *
     * @param LocationRequest $request
     * @return JsonResponse
     */
    public function store(LocationRequest $request): JsonResponse
    {
        $location = Location::create([
            'name' => $request->input('name'),
            'place_id' => $request->input('place_id'),
            'position' => $this->getPosition($request),
        ]);

        return response()->json($location->hideSubData());
    }

    /**
     * Shows a location.
     *
     * @param  string $location_id
     * @return JsonResponse
     */
    public function show(string $location_id): JsonResponse
    {
        $location = Location::with('place')->find($location_id);

        if ($location) {
            return response()->json($location->hideSubData());
        } else {
            abort(404, 'Impossible de trouver l\'emplacement');
        }
    }

    /**
     * Updates a location.
     *
     * @param  LocationRequest $request
     * @param  string          $location_id
     * @return JsonResponse
     */
    public function update(LocationRequest $request, string $location_id): JsonResponse
    {
        $location = Location::with('place')->find($location_id);

        if ($location) {
            $inputs = $request->input();

            if ($position = $this->getPosition($request)) {
                $inputs['position'] = $position;
            }

            return response()->json($location, 201);
        } else {
            abort(404, 'Impossible de trouver l\'emplacement');
        }
    }

    /**
     * Deletes a location.
     *
     * @param  string $location_id
     * @return void
     */
    public function destroy(string $location_id): void
    {
        $location = Location::find($location_id);

        if ($location) {
            $location->delete();

            abort(204);
        } else {
            abort(404, 'Impossible de trouver l\'emplacement');
        }
    }
}

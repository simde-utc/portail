<?php
/**
 * Gère les lieux.
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
     * Nécessité de gérer les lieux.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne(['client-get-locations']),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOne(['client-create-locations']),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOne(['client-set-locations']),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOne(['client-manage-locations']),
            ['only' => ['destroy']]
        );
    }

    /**
     * Liste les lieux.
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
     * Crée un lieu.
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
     * Montre un lieu.
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
     * Met à jour un lieu.
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
     * Supprime un lieu.
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

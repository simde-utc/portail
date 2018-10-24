<?php
/**
 * Gère les lieux.
 *
 * TODO: Déplacer la récupération dans un Trait.
 * TODO: Transformer en abort.
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

class LocationController extends Controller
{
    /**
     * Nécessité de gérer les lieux.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne(
                ['client-get-locations']
            ),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOne(
                ['client-create-locations']
            ),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOne(
                ['client-set-locations']
            ),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOne(
                ['client-manage-locations']
            ),
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
        return response()->json(Location::get(), 200);
    }

    /**
     * Crée un lieu.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $location = Location::create($request->all());

        if ($location) {
            return response()->json($location, 200);
        } else {
            return response()->json(['message' => 'Impossible de créer l\'emplacement'], 500);
        }

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
            return response()->json($location, 200);
        } else {
            return response()->json(['message' => 'Impossible de trouver l\'emplacement'], 500);
        }
    }

    /**
     * Met à jour un lieu.
     *
     * @param  Request $request
     * @param  string  $location_id
     * @return JsonResponse
     */
    public function update(Request $request, string $location_id): JsonResponse
    {
        $location = Location::with('place')->find($location_id);

        if ($location) {
            if ($location->update($request->input())) {
                return response()->json($location, 201);
            } else {
                return response()->json(['message' => 'Impossible d\'actualiser l\'emplacement'], 500);
            }
        } else {
            return response()->json(['message' => 'Impossible de trouver l\'emplacement'], 500);
        }
    }

    /**
     * Supprime un lieu.
     *
     * @param  string  $location_id
     * @return JsonResponse
     */
    public function destroy(string $location_id): JsonResponse
    {
        $location = Location::find($location_id);

        if ($location) {
            if ($location->softDelete()) {
                return response()->json(['message' => 'L\'emplacement a bien été supprimée'], 200);
            } else {
                return response()->json(['message' => 'Erreur lors de la suppression de l\'emplacement'], 500);
            }
        } else {
            return response()->json(['message' => 'Impossible de trouver l\'emplacement'], 500);
        }
    }
}

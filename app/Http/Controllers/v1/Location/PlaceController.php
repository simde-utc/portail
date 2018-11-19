<?php
/**
 * Gère les emplacements de lieux.
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
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-create-locations-places'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-set-locations-places'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('client-manage-locations-places'),
            ['only' => ['destroy']]
        );
    }

    /**
     * Liste les emplacements.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Place::get(), 200);
    }

    /**
     * Créer un emplacement.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $place = Place::create($request->all());

        return response()->json($place, 200);
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

        return response()->json($place, 200);
    }

    /**
     * Met à jour un emplacement.
     *
     * @param Request $request
     * @param string  $place_id
     * @return JsonResponse
     */
    public function update(Request $request, string $place_id): JsonResponse
    {
        $place = $this->getPlace($request, $place_id);

        if ($place->update($request->input())) {
            return response()->json($place, 201);
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

        if ($place->softDelete()) {
            abort(204);
        } else {
            abort(500, 'Erreur lors de la suppression de le lieu');
        }
    }
}

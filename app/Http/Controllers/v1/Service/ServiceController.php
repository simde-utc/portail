<?php
/**
 * Gère les services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Service;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Models\Visibility;
use App\Traits\Controller\v1\HasServices;
use App\Traits\Controller\v1\HasImages;

class ServiceController extends Controller
{
    use HasServices, HasImages;

    /**
     * Nécessité de pouvoir gérer les services.
     * L'affichage est public.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::allowPublic()->matchOne('user-get-services-created', 'client-get-services-created'),
            ['only' => ['index', 'show', 'bulkShow']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOne('user-create-services-created', 'client-create-services-created'),
                ['permission:asso']
            ),
            ['only' => ['store', 'bulkStore']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOne('user-edit-services-created', 'client-edit-services-created'),
                ['permission:service']
            ),
            ['only' => ['update', 'bulkUpdate']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOne('user-remove-services-created', 'client-remove-services-created'),
                ['permission:service']
            ),
            ['only' => ['destroy', 'bulkDestroy']]
        );
    }

    /**
     * Récupère la liste des services.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $services = Service::getSelection();

        return response()->json($services->map(function ($service) {
            return $service->hideData();
        }), 200);
    }

    /**
     * Créer un service.
     *
     * @param ServiceRequest $request
     * @return JsonResponse
     */
    public function store(ServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->all());

        // On affecte l'image si tout s'est bien passé.
        $this->setImage($request, $service, 'services/'.$service->id);

        return response()->json($service->hideSubData(), 200);
    }

    /**
     * Montre un service.
     *
     * @param Request $request
     * @param string  $service_id
     * @return JsonResponse
     */
    public function show(Request $request, string $service_id): JsonResponse
    {
        $service = $this->getService(\Auth::user(), $service_id);

        return response()->json($service->hideSubData(), 200);
    }

    /**
     * Met à jour un service.
     *
     * @param ServiceRequest $request
     * @param string         $service_id
     * @return JsonResponse
     */
    public function update(ServiceRequest $request, string $service_id): JsonResponse
    {
        $service = $this->getService(\Auth::user(), $service_id);

        if ($service->update($request->input())) {
            // On affecte l'image si tout s'est bien passé.
            $this->setImage($request, $service, 'services/'.$service->id);

            return response()->json($service->hideSubData(), 201);
        } else {
            abort(500, 'Impossible de modifier la service');
        }
    }

    /**
     * Supprime un service.
     *
     * @param Request $request
     * @param string  $service_id
     * @return void
     */
    public function destroy(Request $request, string $service_id): void
    {
        $service = $this->getService(\Auth::user(), $service_id);

        if ($service->delete()) {
            $this->deleteImage('services/'.$service->id);

            abort(204);
        } else {
            abort(500, 'Impossible de supprimer la service');
        }
    }
}

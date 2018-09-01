<?php

namespace App\Http\Controllers\v1\Service;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Service;


/**
 * @resource Service
 *
 * Gestion des services
 */
class ServiceController extends Controller
{
	public function __construct() {
		// La récupération des services est publique
		$this->middleware(
			\Scopes::allowPublic()->matchOne('user-get-services', 'client-get-services'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-create-services', 'client-create-services'),
				['permission:asso']
			),
			['only' => ['store']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-edit-services', 'client-edit-services'),
				['permission:service']
			),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-remove-services', 'client-remove-services'),
				['permission:service']
			),
			['only' => ['destroy']]
		);
	}

	/**
	 * List Visibilities
	 * @return JsonResponse
	 */

	public function index(): JsonResponse {
		$services = Service::getSelection()->map(function ($service) {
			return $service->hideData();
		});

		return response()->json($services, 200);
	}

	/**
	 * Create Service
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function store(Request $request): JsonResponse {
		$service = Service::create($request->all());

		if ($service)
			return response()->json($service->hideSubData(), 200);
		else
			abort(500, 'Impossible de créer la service');
	}

	/**
	 * Show Service
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function show($id): JsonResponse {
		$service = Service::find($id);

		if ($service)
			return response()->json($service->hideSubData(), 200);
		else
			abort(404, 'Service non trouvée');
	}

	/**
	 * Update Service
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, string $id): JsonResponse {
		$service = Service::find($id);

		if ($service) {
			if ($service->update($request->input()))
				return response()->json($service->hideSubData(), 201);
			else
				abort(500, 'Impossible de modifier la service');
		}
		else
			abort(404, 'Service non trouvée');
	}

	/**
	 * Delete Service
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$service = Service::find($id);

		if ($service) {
			if ($service->softDelete())
				abort(204);
			else
				abort(500, 'Impossible de supprimer la service');
		}
		else
			abort(404, 'Service non trouvée');
	}
}

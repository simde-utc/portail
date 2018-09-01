<?php

namespace App\Http\Controllers\v1\Service;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Traits\Controller\v1\HasServices;

/**
 * @resource Service
 *
 * Gestion des services
 */
class ServiceController extends Controller
{
	use HasServices;

	public function __construct() {
		// La récupération des services est publique
		$this->middleware(
			\Scopes::allowPublic()->matchOne('user-get-services-created', 'client-get-services-created'),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-create-services-created', 'client-create-services-created'),
				['permission:asso']
			),
			['only' => ['store']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-edit-services-created', 'client-edit-services-created'),
				['permission:service']
			),
			['only' => ['update']]
		);
		$this->middleware(
			array_merge(
				\Scopes::matchOne('user-remove-services-created', 'client-remove-services-created'),
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
		$services = Service::getSelection()->filter(function ($service) {
			return !\Auth::id() || $this->isVisibile($service, \Auth::id());
		})->map(function ($service) {
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
		$service = $this->getService($user, $id);

		return response()->json($service->hideSubData(), 200);
	}

	/**
	 * Update Service
	 *
	 * @param Request $request
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function update(Request $request, string $id): JsonResponse {
		$service = $this->getService($user, $id);

		if ($service->update($request->input()))
			return response()->json($service->hideSubData(), 201);
		else
			abort(500, 'Impossible de modifier la service');
	}

	/**
	 * Delete Service
	 *
	 * @param  int $id
	 * @return JsonResponse
	 */
	public function destroy(string $id): JsonResponse {
		$service = $this->getService($user, $id);

		if ($service->softDelete())
			abort(204);
		else
			abort(500, 'Impossible de supprimer la service');
	}
}

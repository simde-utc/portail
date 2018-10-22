<?php
/**
 * Gère les services favoris des utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasServices;
use App\Models\User;
use App\Models\Asso;
use App\Models\Service;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Visible\Visible;
use App\Interfaces\CanHaveServices;
use App\Traits\HasVisibility;

class ServiceController extends Controller
{
    use HasServices;

    /**
     * Nécessité de pouvoir gérer les services suivis
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-services-followed', 'client-get-services-followed'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-services-followed', 'client-create-services-followed'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-services-followed', 'client-edit-services-followed'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-services-followed', 'client-manage-services-followed'),
            ['only' => ['destroy']]
        );
    }

    /**
     * Liste les services suivis par l'utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);

        $services = $user->followedServices()->getSelection()->map(function ($service) {
            return $service->hideData();
        });

        return response()->json($services, 200);
    }

    /**
     * Ajouter un service suivi par l'utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user_id
     * @return JsonResponse
     */
    public function store(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);
        $services = collect();
        $service_ids = $request->input('service_ids', [$request->input('service_id')]);

        foreach ($service_ids as $service_id) {
            $service = $this->getService($user, $service_id);

            $user->followedServices()->attach($service);
            $services[] = $service;
        }

        $services = $services->map(function ($service) {
            return $service->hideData();
        });

        return response()->json($services, 201);
    }

    /**
     * Montre un service suivi par l'utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user_id
     * @param string                   $id
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $id=null): JsonResponse
    {
        if (is_null($id)) {
            list($user_id, $id) = [$id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $service = $this->getFollowedService($user, $id);

        return response()->json($service->hideSubData(), 200);
    }

    /**
     * Il n'est pas possible de mettre à jour un service suivi par l'utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user_id
     * @param string                   $id
     * @return void
     */
    public function update(Request $request, string $user_id, string $id=null): JsonResponse
    {
        abort(405);
    }

    /**
     * Retire un service suivi par l'utilisateur.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user_id
     * @param string                   $id
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $id=null): JsonResponse
    {
        if (is_null($id)) {
            list($user_id, $id) = [$id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $service = $this->getFollowedService($user, $id);

        $user->followedServices()->detach($service);

        abort(204);
    }
}

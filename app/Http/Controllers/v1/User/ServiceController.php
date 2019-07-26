<?php
/**
 * Manage user favorite services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\{
    HasUserBulkMethods, HasServices
};
use App\Models\User;
use App\Models\Asso;
use App\Models\Service;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserServiceRequest;
use App\Interfaces\CanHaveServices;

class ServiceController extends Controller
{
    use HasUserBulkMethods, HasServices;

    /**
     * Must be able to handle followed services.
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
        // Can index, show and (un)follow and edit services for multiple users in a raw.
        $this->middleware(
            \Scopes::matchAnyClient(),
            ['only' => ['bulkIndex', 'bulkStore', 'bulkShow', 'bulkUpdate', 'bulkDestroy']]
        );
    }

    /**
     * List all services followed by the user.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);
        Service::setUserForVisibility($user);

        $services = $user->followedServices()->getSelection()->map(function ($service) {
            return $service->hideData();
        });

        return response()->json($services->values(), 200);
    }

    /**
     * Add a followed service for the user.
     *
     * @param UserServiceRequest $request
     * @param string             $user_id
     * @return JsonResponse
     */
    public function store(UserServiceRequest $request, string $user_id=null): JsonResponse
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
     * Show service followed by the user.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $service_id
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $service_id=null): JsonResponse
    {
        if (is_null($service_id)) {
            list($user_id, $service_id) = [$service_id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $service = $this->getFollowedService($user, $service_id);

        return response()->json($service->hideSubData(), 200);
    }

    /**
     * It is not possible to update a service followed by the user.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $service_id
     * @return void
     */
    public function update(Request $request, string $user_id, string $service_id=null): void
    {
        abort(405);
    }

    /**
     * Remove a service followed by the user.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $service_id
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $service_id=null): void
    {
        if (is_null($service_id)) {
            list($user_id, $service_id) = [$service_id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $service = $this->getFollowedService($user, $service_id);

        $user->followedServices()->detach($service);

        abort(204);
    }
}

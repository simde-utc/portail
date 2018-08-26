<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Notifications\ExternalNotification;
use App\Interfaces\Model\CanNotify;

/**
 * @resource Notification
 *
 * Les notifications écrits par les utilisateurs
 */
class NotificationController extends Controller
{
    use HasNotifications;

    /**
     * Scopes Notification
     *
     * Les Scopes requis pour manipuler les Notifications
     */
    public function __construct() {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-notifications', 'client-get-notifications'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-notifications', 'client-create-notifications'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-notifications', 'client-edit-notifications'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-notifications', 'client-manage-notifications'),
            ['only' => ['destroy']]
        );
    }


    /**
     * List Notifications
     *
     * Retourne la liste des notifications.
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id = null): JsonResponse {
        $user = $this->getUser($request, $user_id);
        $choices = $this->getChoices($request, ['read', 'unread']);
        $notifications = $user->notifications();

        if (count($choices) === 1) {
            if (in_array('read', $choices))
                $notifications = $notifications->whereNotNull('read_at');
            else
                $notifications = $notifications->whereNull('read_at');
        }

        $notifications = $notifications->getSelection()->map(function ($notification) {
            return $notification->hideData();
        });

        return response()->json($notifications, 200);
    }

    /**
     * Create Notification
     *
     * Créer un notification.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, string $user_id = null): JsonResponse {
        $user = $this->getUser($request, $user_id);

        $user->notify(new ExternalNotification(
            \ModelResolver::getModel($request->input('notifier', 'client'), CanNotify::class),
            $request->input('content'),
            $request->input('action', [])
        ));

        abort(201, 'Notification créée et envoyée');
    }

    /**
     * Show Notification
     *
     * Affiche le notification.
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id = null, string $id = null): JsonResponse {
        if (is_null($id))
            list($user_id, $id) = [$id, $user_id];

        $notification = $this->getUserNotification($request, $user_id, $id);

        return response()->json($notification->hideSubData(), 200);
    }

    /**
     * Update Notification
     *
     * Met à jour le notification.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, string $user_id = null, string $id = null): JsonResponse {
        if (is_null($id))
            list($user_id, $id) = [$id, $user_id];

        $notification = $this->getUserNotification($request, $user_id, $id);

        if ($request->has('read'))
            $notification->update(['read_at' => now()]);
        else if ($request->has('unread'))
            $notification->update(['read_at' => null]);

        return response()->json($notification->hideSubData(), 200);
    }

    /**
     * Delete Notification
     *
     * Supprime le notification.
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request, string $user_id = null, string $id = null): JsonResponse {
        if (is_null($id))
            list($user_id, $id) = [$id, $user_id];

        $notification = $this->getUserNotification($request, $user_id, $id);
        $notification->delete();

        abort(204);
    }
}

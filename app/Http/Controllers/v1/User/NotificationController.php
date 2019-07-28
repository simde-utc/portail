<?php
/**
 * Manage user notifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\{
    HasUserBulkMethods, HasNotifications
};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserNotificationRequest;
use App\Models\Notification;
use App\Notifications\ExternalNotification;
use App\Interfaces\Model\CanNotify;

class NotificationController extends Controller
{
    use HasUserBulkMethods, HasNotifications;

    /**
     * Must be able to manage notifications.
     */
    public function __construct()
    {
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
            ['only' => ['edit']]
        );
        // Can index, show and create notifications for multiple users in a raw.
        $this->middleware(
            \Scopes::matchAnyClient(),
            ['only' => ['bulkIndex', 'bulkStore', 'bulkShow']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-notifications', 'client-manage-notifications'),
            ['only' => ['remove']]
        );
    }

    /**
     * Return the request wich must be executed for an element of the bulk.
     *
     * @param  string  $requestClass
     * @param  Request $baseRequest
     * @param  array   $args
     * @return Request
     */
    protected function getRequestForBulk(string $requestClass, Request $baseRequest, array $args): Request
    {
        $request = parent::getRequestForBulk($requestClass, $baseRequest, $args);

        if (isset($request->input('data')[$args[0]])) {
            $request->merge(\array_merge($request->input(), ['data' => $request->input('data')[$args[0]]]));
        }

        return $request;
    }

    /**
     * List all user notifications.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);
        $choices = $this->getChoices($request, ['read', 'unread']);
        $notifications = $user->notifications();

        if (count($choices) === 1) {
            if (in_array('read', $choices)) {
                $notifications = $notifications->whereNotNull('read_at');
            } else {
                $notifications = $notifications->whereNull('read_at');
            }
        }

        $notifications = $notifications->getSelection()->map(function ($notification) {
            return $notification->hideData();
        });

        return response()->json($notifications, 200);
    }

    /**
     * Create a notification for the user.
     *
     * @param UserNotificationRequest $request
     * @param string                  $user_id
     * @return mixed
     */
    public function store(UserNotificationRequest $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);
        $notifier = \Scopes::getClient($request);

        if ($request->input('notifier') === 'asso') {
            $notifier = $notifier->asso;
        }

        $user->notify($notification = new ExternalNotification(
            $notifier,
            $request->input('subject'),
            $request->input('content'),
            $request->input('html'),
            $request->input('action', []),
            $request->input('data', []),
            $request->input('exceptedVia', [])
        ));

        return response()->json($notification->toArray($user), 200);
    }

    /**
     * Show a user notification.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $notification_id
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id=null, string $notification_id=null): JsonResponse
    {
        if (is_null($notification_id)) {
            list($user_id, $notification_id) = [$notification_id, $user_id];
        }

        $notification = $this->getUserNotification($request, $user_id, $notification_id);

        return response()->json($notification->hideSubData(), 200);
    }

    /**
     * Update a user notification.
     *
     * @param UserNotificationRequest $request
     * @param string                  $user_id
     * @param string                  $notification_id
     * @return JsonResponse
     */
    public function update(UserNotificationRequest $request, string $user_id=null, string $notification_id=null): JsonResponse
    {
        if (is_null($notification_id)) {
            list($user_id, $notification_id) = [$notification_id, $user_id];
        }

        $notification = $this->getUserNotification($request, $user_id, $notification_id);

        if ($request->has('read')) {
            $notification->update(['read_at' => now()]);
        } else if ($request->has('unread')) {
            $notification->update(['read_at' => null]);
        }

        return response()->json($notification->hideSubData(), 200);
    }

    /**
     * Delete a user notification.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $notification_id
     * @return void
     */
    public function destroy(Request $request, string $user_id=null, string $notification_id=null): void
    {
        if (is_null($notification_id)) {
            list($user_id, $notification_id) = [$notification_id, $user_id];
        }

        $notification = $this->getUserNotification($request, $user_id, $notification_id);
        $notification->delete();

        abort(204);
    }
}

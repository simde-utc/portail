<?php
/**
 * Gère les notification utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use App\Traits\Controller\v1\HasNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Notifications\ExternalNotification;
use App\Interfaces\Model\CanNotify;

class NotificationController extends Controller
{
    use HasNotifications;

    /**
     * Nécessite de pouvoir gérer les notifications.
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
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-notifications', 'client-manage-notifications'),
            ['only' => ['destroy']]
        );
    }

    /**
     * Liste les notifications de l'utilisateur.
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
     * Créer une notification pour l'utlisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @return void
     */
    public function store(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);

        $user->notify(new ExternalNotification(
            \ModelResolver::getModel($request->input('notifier', 'client'), CanNotify::class),
            $request->input('content'),
            $request->input('action', [])
        ));

        abort(201, 'Notification créée et envoyée');
    }

    /**
     * Montre une notification de l'utlisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $id
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id=null, string $id=null): JsonResponse
    {
        if (is_null($id)) {
            list($user_id, $id) = [$id, $user_id];
        }

        $notification = $this->getUserNotification($request, $user_id, $id);

        return response()->json($notification->hideSubData(), 200);
    }

    /**
     * Met à jour une notification de l'utlisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $id
     * @return JsonResponse
     */
    public function update(Request $request, string $user_id=null, string $id=null): JsonResponse
    {
        if (is_null($id)) {
            list($user_id, $id) = [$id, $user_id];
        }

        $notification = $this->getUserNotification($request, $user_id, $id);

        if ($request->has('read')) {
            $notification->update(['read_at' => now()]);
        } else if ($request->has('unread')) {
            $notification->update(['read_at' => null]);
        }

        return response()->json($notification->hideSubData(), 200);
    }

    /**
     * Supprime une notification de l'utlisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $id
     * @return void
     */
    public function destroy(Request $request, string $user_id=null, string $id=null): JsonResponse
    {
        if (is_null($id)) {
            list($user_id, $id) = [$id, $user_id];
        }

        $notification = $this->getUserNotification($request, $user_id, $id);
        $notification->delete();

        abort(204);
    }
}

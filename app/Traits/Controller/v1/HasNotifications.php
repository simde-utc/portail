<?php
/**
 * Adds the controller an access to Notifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Traits\Controller\v1\HasUsers;
use App\Models\User;
use Illuminate\Http\Request;

trait HasNotifications
{
    use HasUsers;

    /**
     * Retrieves user's notifications.
     *
     * @param  Request $request
     * @param  string  $user_id
     * @param  string  $notification_id
     * @return mixed
     */
    public function getUserNotification(Request $request, string $user_id=null, string $notification_id)
    {
        $user = $this->getUser($request, $user_id);
        $notification = $user->notifications()->findSelection($notification_id);

        if ($notification) {
            return $notification;
        } else {
            abort(404, 'Cette notification n\'existe pas pour l\'utilisateur');
        }
    }
}

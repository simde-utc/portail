<?php

namespace App\Traits\Controller\v1;

use App\Traits\Controller\v1\HasUsers;
use App\Models\User;
use Illuminate\Http\Request;

trait HasNotifications
{
	use HasUsers;

	public function getUserNotification(Request $request, string $user_id = null, string $id) {
		$user = $this->getUser($request, $user_id);
		$notification = $user->notifications()->find($id);

		if ($notification)
			return $notification;
		else
			abort(404, 'Cette notification n\'existe pas pour l\'utilisateur');
	}
}

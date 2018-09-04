<?php

namespace App\Traits\Controller\v1;

use App\Traits\HasVisibility;
use App\Models\Room;
use App\Models\Model;
use Illuminate\Http\Request;

trait HasRooms
{
	use HasVisibility;

	protected function getRoom(string $id) {
		$room = Room::find($id);

		if ($room) {
			if (!$this->isVisible($room, $user->id))
				abort(403, 'Vous n\'avez pas le droit de voir cette salle');

			return $room;
		}

		abort(404, 'Impossible de trouver la salle de réservation');
	}
}

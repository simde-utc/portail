<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\User;
use App\Models\Service;
use App\Models\Model;
use Illuminate\Http\Request;
use App\Traits\HasVisibility;

trait HasServices
{
	use HasUsers, HasVisibility;

	protected function getService(User $user = null, string $id) {
		$service = Service::find($id);

		if ($service) {
			if (!$this->isVisible($service, $user->id))
				abort(403, 'Vous n\'avez pas le droit de voir ce service');

			return $service;
		}

		abort(404, 'Impossible de trouver le service');
	}

	protected function getFollowedService(User $user = null, string $id) {
		$service = $user->services()->find($id);

		if ($service)
			return $service;

		abort(404, 'Impossible de trouver le service ou il n\'est pas suivi par l\'utilisateur');
	}
}

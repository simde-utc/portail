<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasVisibility;

trait HasGroups
{
	use HasVisibility;

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		if ($model->user_id && $model->user_id == $user_id)
			return true;

		try {
			return $model->hasOneMember(\Auth::id());
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Renvoie le groupe demandé
	 *
	 * @param Request $request
	 * @param int $group_id
	 * @return Group
	 */
	protected function getGroup(Request $request, string $group_id): Group {
		$group = Group::find($group_id);

		if ($group) {
			if ($this->isVisible($group))
				return $group;
			else
				abort(403, "Vous n\'avez pas le droit de voir le groupe");
		}

		abort(404, "Groupe non trouvé");
	}
}

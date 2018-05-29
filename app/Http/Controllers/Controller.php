<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Permet de savoir quoi afficher
	 * @param  Request $request
	 * @param  array $choices
	 * @return array
	 * @throws PortailException
	 */
	protected function getChoices(Request $request, array $choices) {
		$only = $request->input('only') ? explode(',', $request->input('only')) : [];
		$except = $request->input('except') ? explode(',', $request->input('except')) : [];

		if (count(array_intersect($only, $choices)) !== count($only) || count(array_intersect($except, $choices)) !== count($except))
			throw new PortailException('Il n\'est possible de spécifier pour only et except que: '.implode(', ', $choices));

		if (count($only) > 0)
			$choices = $only;

		foreach ($except as $choice) {
			if (($key = array_search($choice, $choices)) !== false)
				unset($choices[$key]);
		}

		return $choices;
	}

	/**Cache les données des utilisateurs dans une collection
	 *
	 * @param Request $request
	 * @param Collection $users
	 * @param bool $hidePivot
	 * @return Collection|null
	 */
	protected function hideUsersData(Request $request, Collection $users, bool $hidePivot = true): ?Collection {

		if ($users === null)
			return null;

		foreach ($users as $user) {
			$user->name = $user->firstname.' '.strtoupper($user->lastname);
			$user->makeHidden(['firstname', 'lastname', 'email', 'last_login_at', 'created_at', 'updated_at']);
			$this->hidePivotData($request, $user, $hidePivot);
		}

		return $users;
	}

	/**Cache les données d'un utilisateur
	 *
	 * @param Request $request
	 * @param User $user
	 * @param bool $hidePivot
	 * @return User|null
	 */
	protected function hideUserData(Request $request, User $user, bool $hidePivot = true): ?User {

		if ($user === null)
			return null;

		$user->name = $user->firstname.' '.strtoupper($user->lastname);

		if ($user->id === \Auth::id())
			$user->me = true;

		$user->makeHidden(['firstname', 'lastname', 'email', 'last_login_at', 'created_at', 'updated_at']);

		return $this->hidePivotData($request, $user, $hidePivot);
	}

	/**Cache les données de pivot dans un modèle
	 *
	 * @param Request $request
	 * @param Model $model
	 * @param bool $hidePivot
	 * @return Model
	 */
	protected function hidePivotData(Request $request, Model $model, bool $hidePivot = true): ?Model {
		if ($model === null)
			return null;

		if ($hidePivot)
			$model->makeHidden('pivot');
		else {
			$model->pivot->makeHidden(['group_id', 'user_id']);

			if ($model->pivot->semester_id === 0)
				$model->pivot->makeHidden('semester_id');

			if (is_null($model->pivot->role_id))
				$model->pivot->makeHidden('role_id');

			if (is_null($model->pivot->validated_by))
				$model->pivot->makeHidden('validated_by');
		}

		return $model;
	}
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Asso;
use App\Models\Group;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
	/**
	 * Renvoie les inforamtions sur un utilisateur via son id ou sur l'utilisateur actuellement connecté
	 * @param Request $request
	 * @param int|null $user_id
	 * @param bool $accessOtherUsers = false
	 * @return User
	 */
	 protected function getUser(Request $request, int $user_id = null, bool $accessOtherUsers = false): User {
		if ($accessOtherUsers)
			$user = $user_id ? User::find($user_id) : \Auth::user();
		else {
			if (\Scopes::isClientToken($request))
				$user = User::find($user_id ?? null);
			else {
				$user = \Auth::user();

				if (!is_null($user_id) && $user->id !== $user_id)
				abort(403, 'Vous n\'avez pas le droit d\'accéder aux données d\'un autre utilisateur');
			}
		}

 		if ($user)
 			return $user;
 		else
 			abort(404, "Utilisateur non trouvé");
 	}

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

		if ($user->id === \Auth::id())
			$user->me = true;

		$user->makeHidden(['firstname', 'lastname', 'email', 'is_active', 'last_login_at', 'created_at', 'updated_at']);

		return $this->hidePivotData($request, $user, $hidePivot);
	}

	protected function hideAssoData(Request $request, Asso $asso) {
		return $asso->makeHidden(['login', 'description', 'type_asso_id', 'parent_id', 'created_at', 'updated_at', 'deleted_at']);
	}

	protected function hideGroupData(Request $request, Group $group) {
		return $group->makeHidden(['icon', 'created_at', 'updated_at', 'deleted_at']);
	}

	protected function hideClientData(Request $request, Client $client) {
		return $client->makeHidden(['user_id', 'redirect', 'personal_access_client', 'password_client', 'revoked', 'asso_id', 'created_at', 'updated_at', 'scopes']);
	}

	protected function hideData(Request $request, $data) {
		if ($data != null) {
			if ($data instanceof User)
				$data = $this->hideUserData($request, $data);
			else if ($data instanceof Asso)
				$data = $this->hideAssoData($request, $data);
			else if ($data instanceof Group)
				$data = $this->hideGroupData($request, $data);
			else if ($data instanceof Client)
				$data = $this->hideClientData($request, $data);

			$namespace = explode('\\', get_class($data));
			$data['type'] = strtolower(end($namespace));
		}

		return $data;
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

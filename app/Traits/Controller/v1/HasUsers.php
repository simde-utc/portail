<?php

namespace App\Traits\Controller\v1;

use App\Exceptions\PortailException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasUsers
{
	/**
	 * Renvoie les informations sur un utilisateur via son id ou sur l'utilisateur actuellement connecté
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
}

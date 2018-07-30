<?php

namespace App\Traits\Controller\v1;

use App\Traits\HasVisibility;
use Illuminate\Http\Request;

trait HasContacts
{
	use HasVisibility;

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée on vérifie si on a le droit d'accès
		return $model->owned_by->isContactAccessibleBy($user_id);
    }

	protected function checkTokenRights(Request $request, string $verb = 'get') {
		if (!\Scopes::hasOne($request, \Scopes::getTokenType($request).'-get-contacts-'.\ModelResolver::getCategory($request->resource)))
			abort(503, 'L\'application n\'a pas le droit de voir les contacts de cette ressource');
	}

	public function getContact(Request $request, string $verb = 'get') {
		$this->checkTokenRights($request, $verb);
		$contact = $request->resource->contacts()->where('id', $request->contact)->first();

		if ($contact) {
			if (\Auth::id()) {
				if (!$this->isVisible($contact, \Auth::id()))
					abort(503, 'Vous n\'avez pas le droit de voir ce contact');

				if ($verb !== 'get' && !$request->resource->isContactManageableBy(\Auth::id()))
					abort(503, 'Il n\'est pas possible à l\'utilisateur de gérer un contact pour cette ressource');
			}

			return $contact;
		}
		else
			abort(404, "Ce contact n'existe pas pour cette ressource");
	}
}

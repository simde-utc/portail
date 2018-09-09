<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasCreatorsAndOwners
{
	use HasOwners;

	// Le créateur peut être multiple: le user, l'asso ou le client courant. Ou autre. Mais reste dépendant du owner (par rapport aux droits)
	protected function getCreatorFromOwner(Request $request, Model $owner, string $modelName, string $modelText, string $verb = 'create') {
		if ($request->input('created_by_type', 'user') === 'user'
			&& \Auth::id()
			&& $request->input('created_by_id', \Auth::id()) === \Auth::id()
			&& \Scopes::hasOne($request, [\Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-'.\ModelResolver::getName($owner).'s-owned-user', \Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-users-created']))
			$creater = \Auth::user();

		else if ($request->input('created_by_type', 'client') === 'client'
			&& $request->input('created_by_id', \Scopes::getClient($request)->id) === \Scopes::getClient($request)->id
			&& \Scopes::hasOne($request, [\Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-'.\ModelResolver::getName($owner).'s-owned-client', \Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-clients-created']))
			$creater = \Scopes::getClient($request);

		else if ($request->input('created_by_type') === 'asso'
			&& $request->input('created_by_id', \Scopes::getClient($request)->asso->id) === \Scopes::getClient($request)->asso->id
			&& \Scopes::hasOne($request, [\Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-'.\ModelResolver::getName($owner).'s-owned-asso', \Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-assos-created']))
			$creater = \Scopes::getClient($request)->asso;

		else
			$creater = $this->getMorph($request, $modelName, $modelText, $verb, 'created');

		return $creater;
	}
}

<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait HasCreatorsAndOwnersAndValidators
{
	use HasCreatorsAndOwners;

	protected function getValidatorFromOwner(Request $request, Model $owner, string $modelName, string $modelText, string $verb = 'create') {
		if ($request->input('validated_by_type') === 'user'
			&& \Auth::id()
			&& $request->input('validated_by_id', \Auth::id()) === \Auth::id()
			&& \Scopes::hasOne($request, [\Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-'.\ModelResolver::getName($owner).'s-validated-user', \Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-users-validated']))
			$validator = \Auth::user();

		else if ($request->input('validated_by_type') === 'client'
			&& $request->input('validated_by_id', \Scopes::getClient($request)->id) === \Scopes::getClient($request)->id
			&& \Scopes::hasOne($request, [\Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-'.\ModelResolver::getName($owner).'s-validated-client', \Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-clients-validated']))
			$validator = \Scopes::getClient($request);

		else if ($request->input('validated_by_type') === 'asso'
			&& $request->input('validated_by_id', \Scopes::getClient($request)->asso->id) === \Scopes::getClient($request)->asso->id
			&& \Scopes::hasOne($request, [\Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-'.\ModelResolver::getName($owner).'s-validated-asso', \Scopes::getTokenType($request).'-'.$verb.'-'.$modelName.'s-assos-validated']))
			$validator = \Scopes::getClient($request)->asso;

		else
			$validator = $this->getMorph($request, $modelName, $modelText, $verb, 'validated');

		if (!$owner->{'is'.ucfirst($modelName).'ValidableBy'}($validator))
			abort(403, 'Le valideur n\'est pas autorisé à valider cette réservation');

		return $validator;
	}
}

<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasMorphs
{
	protected function getMorph(Request $request, string $modelName, string $modelText, string $verb = 'create', string $type = 'created', string $action = 'ManageableBy') {
		$scopeHead = \Scopes::getTokenType($request);
		$scope = $scopeHead.'-'.$verb.'-'.$modelName.'s-'.$request->input($type.'_by_type', $scopeHead).'s-'.$type;

		if ($type === 'owned')
			$scope = array_keys(\Scopes::getRelatives($scopeHead.'-'.$verb.'-'.$modelName.'s-'.$request->input($type.'_by_type').'s-'.$type));

		if (!\Scopes::hasOne($request, $scope))
			abort(403, 'Il ne vous est pas autorisé de créer de '.$modelName.'s');

		if ($request->filled($type.'_by_type')) {
			if ($request->filled($type.'_by_id')) {
				$createrOrOwner = \ModelResolver::getModel($request->input($type.'_by_type'))->find($request->input($type.'_by_id'));

				if (\Auth::id() && !$createrOrOwner->${'is'.ucfirst($modelName).$action}(\Auth::id()))
					abort(403, 'L\'utilisateur n\'a pas les droits');
			}
			else if ($request->input($type.'_by_type', 'client') === 'client')
				$createrOrOwner = \Scopes::getClient($request);
			else if ($request->input($type.'_by_type', 'client') === 'asso')
				$createrOrOwner = \Scopes::getClient($request)->asso;
		}

		if (!isset($createrOrOwner))
			$createrOrOwner = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();

		if (!($createrOrOwner instanceof \App\Interfaces\${'CanHave'.$modelName.'s'}))
			abort(400, 'L\'instance n\'a pas le droit de posséder de '.$modelName.'s');

		return $createrOrOwner;
	}
}

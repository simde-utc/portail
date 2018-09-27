<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasOwners
{
	use HasMorphs;

	protected function getOwner(Request $request, string $modelName, string $modelText, string $verb = 'create') {
		return $this->getMorph($request, $modelName, $modelText, $verb, 'owned');
	}
}

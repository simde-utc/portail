<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasValidators
{
	use HasMorphs;

	protected function getValidator(Request $request, string $modelName, string $modelText, string $verb = 'create') {
		return $this->getMorph($request, $modelName, $modelText, $verb, 'validated');
	}
}

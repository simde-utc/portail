<?php

namespace App\Traits\Controller\v1;

use Illuminate\Http\Request;

trait HasCreators
{
	use HasMorphs;

	// Le créateur peut être multiple: le user, l'asso ou le client courant. Ou autre
	protected function getCreater(Request $request, string $modelName, string $modelText, string $verb = 'create') {
		$creater = $this->getMorph($request, $modelName, $modelText, $verb, 'created');
	}
}

<?php

namespace App\Traits\Controller\v1;

use App\Models\Asso;
use Illuminate\Http\Request;

trait HasAssos
{
	/**
	 * Récupère une association par son id si elle existe
	 * @param Request $request
	 * @param $asso_id
	 * @return Asso
	 */
	protected function getAsso(Request $request, int $asso_id, bool $withTrashed = false): Asso {
		$asso = Asso::find($asso_id);

		if ($asso)
			return $asso;
		else
			abort(404, "Assocation non trouvée");
	}
}

<?php

namespace App\Traits\Controller\v1;

use App\Models\Asso;
use App\Models\Semester;
use Illuminate\Http\Request;

trait HasAssos
{
	/**
	 * Récupère une association par son id si elle existe
	 * @param Request $request
	 * @param $asso_id
	 * @return Asso
	 */
	protected function getAsso(Request $request, $asso_id): Asso {
		if (is_numeric($asso_id))
			$asso = Asso::find($asso_id);
		else
			$asso = Asso::findByLogin($asso_id);

		if ($asso)
			return $asso;
		else
			abort(404, "Assocation non trouvée");
	}
}

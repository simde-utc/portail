<?php

namespace App\Traits\Model;

use NastuzziSamy\Laravel\Traits\HasStages as BaseHasStages;

Trait HasStages {
    use BaseHasStages;

	public function hideData(bool $addModelName = false) {
		$model = parent::hideData($addModelName);

		// Si on affiche par étage mais que le parent_id manque
		if ((\Request::filled('stage') || \Request::filled('stages')) && !in_array('children', array_keys($model->toArray())))
			$model = $model->makeVisible('parent_id');

		return $model;
    }
}

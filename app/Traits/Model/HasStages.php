<?php
/**
 * Add a staged selector and improves the caching method.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use NastuzziSamy\Laravel\Traits\HasStages as BaseHasStages;

trait HasStages
{
    use BaseHasStages;

    /**
     * This method automatically caches model's data for the JSON response.
     * Overide HasHiddenData trait's method.
     *
     * @param boolean $addModelName
     * @return mixed
     */
    public function hideData(bool $addModelName=false)
    {
        $model = parent::hideData($addModelName);

        // If we display by stage but the prant_id is missing.
        if ((\Request::filled('stage') || \Request::filled('stages')) && !in_array('children', array_keys($model->toArray()))) {
            $model = $model->makeVisible('parent_id');
        }

        return $model;
    }
}

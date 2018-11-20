<?php
/**
 * Ajoute un sélecteur étagé et améliore la méthode pour cacher les données.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use NastuzziSamy\Laravel\Traits\HasStages as BaseHasStages;
use Illuminate\Database\Eloquent\Model;

trait HasStages
{
    use BaseHasStages;

    /**
     * Cette méthode permet de cacher automatiquement des données du modèle pour le retour json.
     * Override de la méthode du trait HasHiddenData.
     *
     * @param boolean $addModelName
     * @return Model|User
     */
    public function hideData(bool $addModelName=false)
    {
        $model = parent::hideData($addModelName);

        // Si on affiche par étage mais que le parent_id manque.
        if ((\Request::filled('stage') || \Request::filled('stages')) && !in_array('children', array_keys($model->toArray()))) {
            $model = $model->makeVisible('parent_id');
        }

        return $model;
    }
}

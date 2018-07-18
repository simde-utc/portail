<?php

namespace App\Interfaces\Model;

use App\Models\Model;

Interface CanHideData {
    /**
     * Cette méthode permet de cacher automatiquement des données du modèle pour le retour json
     * @param  array  $params Paramètres optionnels (à choisir d'utiliser ou non en fonction du modèle)
     * @return Model
     */
    public function hideData(array $params = []): Model;
}

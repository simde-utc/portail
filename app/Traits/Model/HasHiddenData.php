<?php

namespace App\Traits\Model;

use App\Models\Model;

Trait HasHiddenData {
    /**
     * Retourne le type de modèle que c'est
     * @return string
     */
    public function getModelAttribute(): string {
        return \ModelResolver::getName($this);
    }

    /**
     * Cette méthode permet de cacher automatiquement des données des sous-modèles pour le retour json
     * @return Model
    */
    public function hideSubData(): Model {
        foreach ($this->with ?? [] as $sub) {
            if ($this->$sub)
                $this->$sub = $this->$sub->hideData();
        }

        return $this;
    }

    /**
    * Cette méthode permet de cacher automatiquement des données du modèle pour le retour json
    * @return Model
    */
    public function hideData(): Model {
        $this->makeHidden(array_diff(
            array_keys($this->toArray()),
            $this->must ?? [],
            ['id', 'name', 'model'] // On affiche au moins l'id, le nom et le modèle !
        ));

        // On fait définir l'attibut modèle
        $this->model = $this->model;

        return $this->hideSubData() ?? $this;
    }
}

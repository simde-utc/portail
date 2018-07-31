<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Model;

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
     * @return Model/User
    */
    public function hideSubData(bool $addSubModelName = false) {
        foreach ($this->with ?? [] as $sub) {
            if ($this->$sub) {
                if ($this->$sub instanceof Model)
                    $this->$sub = $this->$sub->hideData($addSubModelName);
                else {
                    foreach ($this->$sub as $index => $subSub) {
                        if ($subSub instanceof Model) {
                            $this->$sub[$index] = $subSub->hideData($addSubModelName);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
    * Cette méthode permet de cacher automatiquement des données du modèle pour le retour json
    * @return Model/User
    */
    public function hideData(bool $addModelName = false) {
        $this->makeHidden(array_diff(
            array_keys($this->toArray()),
            $this->must ?? [],
            ['id', 'name', 'model', 'pivot'] // On affiche au moins l'id, le nom et le modèle !
        ));

        // On fait définir l'attibut modèle si on le demande
        if ($addModelName)
            $this->model = $this->model;

        return $this->hideSubData() ?? $this;
    }
}

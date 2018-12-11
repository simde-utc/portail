<?php
/**
 * Cache des données par défaut pour les exporter.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Model;

trait HasHiddenData
{
    /**
     * Retourne le type de modèle que c'est.
     *
     * @return string
     */
    public function getModelAttribute(): string
    {
        return \ModelResolver::getNameFromObject($this);
    }

    public function getMustFields() {
        return array_merge(
            ($this->optional ?? []),
            ($this->must ?? []),
            ['id', 'name', 'model', 'pivot']
        );
    }

    /**
     * Cette méthode permet de cacher automatiquement des données des sous-modèles pour le retour json.
     *
     * @param boolean $addSubModelName
     * @return mixed
     */
    public function hideSubData(bool $addSubModelName=false)
    {
        $visibles = array_keys($this->toArray());
        $toHide = array_merge(
            ($this->with ?? []),
            ($this->optional ?? [])
        );

        foreach ($toHide as $sub) {
            $addModelName = $addSubModelName || in_array($sub, ($this->withModelName ?? []));

            if (in_array($sub, $visibles)) {
                if ($this->$sub instanceof Model) {
                    $this->$sub = $this->$sub->hideData($addModelName);
                } else {
                    if ((is_array($this->$sub) || $this->$sub instanceof \Countable) && count($this->$sub) > 0) {
                        foreach ($this->$sub as $index => $subSub) {
                            if ($subSub instanceof Model) {
                                $this->$sub[$index] = $subSub->hideData($addModelName);
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Cette méthode permet de cacher automatiquement des données du modèle pour le retour json.
     *
     * @param boolean $addModelName
     * @return mixed
     */
    public function hideData(bool $addModelName=false)
    {
        $this->makeHidden(array_diff(
            array_keys($this->toArray()),
            $this->getMustFields()
            // On affiche au moins l'id, le nom et le modèle !
        ));

        // On fait définir l'attibut modèle si on le demande.
        if ($addModelName) {
            $this->model = $this->model;
        }

        return ($this->hideSubData($addModelName) ?? $this);
    }
}

<?php
/**
 * Hide data by default to exporte it.
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
     * Return the model type.
     *
     * @return string
     */
    public function getModelAttribute(): string
    {
        return \ModelResolver::getNameFromObject($this);
    }

    /**
     * Return all required fields.
     *
     * @return array
     */
    public function getMustFields()
    {
        return array_merge(
            ($this->optional ?? []),
            ($this->must ?? []),
            ['id', 'name', 'model', 'pivot']
        );
    }

    /**
     * This method hides automatically data from the sub-model for the JSOn response.
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
     * This method hides automatically data from the model for the JSOn response.
     *
     * @param boolean $addModelName
     * @return mixed
     */
    public function hideData(bool $addModelName=false)
    {
        $this->makeHidden(array_diff(
            array_keys($this->toArray()),
            $this->getMustFields()
            // At least the ID, the name and the Model are displayed.
        ));

        // Model attribute definition if asked.
        if ($addModelName) {
            $this->model = $this->model;
        }

        return ($this->hideSubData($addModelName) ?? $this);
    }
}

<?php
/**
 * Ajoute un sélecteur concernant le possésseur.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait HasOwnerSelection
{
    /**
     * Relation avec le possésseur.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo();
    }

    /**
     * Sélecteur possésseur.
     *
     * @param  Builder $query
     * @param  string  $owner_type
     * @param  string  $owner_id
     * @return mixed
     */
    public function scopeOwner(Builder $query, string $owner_type, string $owner_id=null)
    {
        $query = $query->where('owned_by_type', \ModelResolver::getModelName($owner_type));

        if ($owner_id) {
            $query = $query->where('owned_by_id', $owner_id);
        }

        return $query;
    }
}

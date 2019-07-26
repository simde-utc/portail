<?php
/**
 * Add a selector concerning the creator.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait HasCreatorSelection
{
    /**
     * Relation with the creator.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo();
    }

    /**
     * Creator selector.
     *
     * @param  Builder $query
     * @param  string  $creator_type
     * @param  string  $creator_id
     * @return mixed
     */
    public function scopeCreator(Builder $query, string $creator_type, string $creator_id=null)
    {
        $query = $query->where('created_by_type', \ModelResolver::getModelName($creator_type));

        if ($creator_id) {
            $query = $query->where('created_by_id', $creator_id);
        }

        return $query;
    }
}

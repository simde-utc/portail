<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

Trait HasCreatorSelection {
    public function created_by() {
        return $this->morphTo();
    }

    public function scopeCreator(Builder $query, string $creator_type, string $creator_id = null) {
        $query = $query->where('created_by_type', \ModelResolver::getModelName($creator_type));

        if ($creator_id)
            $query = $query->where('created_by_id', $creator_id);

        return $query;
    }
}

<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

Trait HasOwnerSelection {
    public function owned_by() {
        return $this->morphTo();
    }

    public function scopeOwner(Builder $query, string $owner_type, string $owner_id = null) {
        $query = $query->where('owned_by_type', \ModelResolver::getModelName($owner_type));

        if ($owner_id)
            $query = $query->where('owned_by_id', $owner_id);

        return $query;
    }
}

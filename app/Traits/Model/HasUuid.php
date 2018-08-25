<?php

namespace App\Traits\Model;

Trait HasUuid {
    protected static function bootHasUuid() {
        static::creating(function ($model) {
            $keys = $model->getKeyName();

            if (!is_array($keys))
                $keys = [$model->getKeyName()];

            foreach ($keys as $key)
                $model->$key = $model->$key ?: \Uuid::generate()->string;
        });
    }
}

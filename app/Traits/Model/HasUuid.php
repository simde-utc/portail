<?php

namespace App\Traits\Model;

Trait HasUuid {
    protected static function bootHasUuid() {
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = $model->{$model->getKeyName()} ?: \Uuid::generate()->string;
        });
    }
}

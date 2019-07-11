<?php
/**
 * Indicates UUIDs' model.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

trait HasUuid
{
    /**
     * At the model's launch, creates dynamically UUIDs.
     *
     * @return void
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            foreach ((array) $model->getKeyName() as $key) {
                $model->$key = $model->$key ?: \Uuid::generate()->string;
            }
        });
    }
}

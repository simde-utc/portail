<?php
/**
 * Indique que le modèle possède des uuids.
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
     * Au lancement du modèle, crée dynamiquement les UUIDs
     * @return void
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $keys = $model->getKeyName();

            if (!is_array($keys)) {
                $keys = [$model->getKeyName()];
            }

            foreach ($keys as $key) {
                $model->$key = $model->$key ?: \Uuid::generate()->string;
            }
        });
    }
}

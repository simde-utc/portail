<?php
/**
 * Modèle correspondant aux notifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class Notification extends Model
{
    protected $fillable = [
        'type', 'notifiable_id', 'notifiable_type', 'data', 'created_at', 'updated_at', 'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected $with = [
        'notifiable',
    ];

    protected $withModelName = [
        'notifiable',
    ];

    protected $hidden = [
        'type', 'notifiable_id', 'notifiable_type',
    ];

    protected $must = [
        'notifiable', 'data', 'created_at', 'read_at',
    ];

    protected $selection = [
        'paginate' => 10,
        'filter' => [],
    ];

    /**
     * Relation avec l'entité notifiée
     *
     * @return mixed
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}

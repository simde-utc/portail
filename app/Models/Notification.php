<?php

namespace App\Models;

class Notification extends Model
{
    protected $fillable = [
		'type', 'notifiable_id', 'notifiable_type', 'data', 'created_at', 'updated_at', 'read_at',
	];

    protected $spatialFields = [
        'position',
    ];

	protected $with = [
		'notifiable',
	];

	protected $withModelName = [
		'notifiable',
	];

    protected $hidden = [
        'notifiable_id', 'notifiable_type',
    ];

    public function getTypeAttribute() {
        return resolve($this->type);
    }

    public function notifiable() {
        return $this->morphTo();
    }
}

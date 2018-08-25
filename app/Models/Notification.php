<?php

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

    public function notifiable() {
        return $this->morphTo();
    }
}

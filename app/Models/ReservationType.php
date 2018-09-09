<?php

namespace App\Models;

class ReservationType extends Model
{
    protected $table = 'rooms_reservations_types';

    protected $fillable = [
        'name', 'type', 'need_validation',
    ];

    protected $casts = [
        'need_validation' => 'boolean',
    ];

    protected $must = [
        'name', 'type', 'need_validation',
    ];

    protected function reservations() {
        return $this->hasMany(Reservation::class);
    }
}

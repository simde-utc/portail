<?php

namespace App\Models;

class ReservationType extends Model
{
    protected $table = 'reservations_types';

    protected $fillable = [
        'name', 'type', 'need_validation',
    ];

    protected $must = [
        'name', 'type', 'need_validation', 
    ];

    protected function reservations() {
        return $this->hasMany(Reservation::class);
    }
}

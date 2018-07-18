<?php

namespace App\Models;

class Reservation extends Model // TODO $must $fillable
{
    public function asso() {
        return $this->belongsTo('App\Models\Asso');
    }
}

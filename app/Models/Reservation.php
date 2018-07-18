<?php

namespace App\Models;

class Reservation extends Model
{
    public function hideData(array $params = []): Model {
        return $this; // TODO
    }

    public function asso() {
        return $this->belongsTo('App\Models\Asso');
    }
}

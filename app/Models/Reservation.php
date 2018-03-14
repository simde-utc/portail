<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public function asso() {
        return $this->belongsTo('App\Models\Asso');
    }
}

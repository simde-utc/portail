<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asso extends Model
{
    public function members() {
        // hasMany through AssoMember
    }

    public function contact() {
        // hasOne
    }

    public function rooms() {
        // hasMany
    }

    public function reservations() {
        // hasMany
    }

    public function articles() {
        // hasMany
    }

    public function events() {
        // hasMany
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{    
    public function contactable() {
        return $this->morphTo();
    }

    public function type() {
        return $this->belongsTo('App\Models\ContactType');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasVisibility;

class Contact extends Model
{
    use HasVisibility;
    
    public function contactable() {
        return $this->morphTo();
    }

    public function type() {
        return $this->belongsTo('App\Models\ContactType');
    }
}

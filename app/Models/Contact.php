<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{    
    protected $hidden = ['contact_type_id', 'visibility_id', 'contactable_id', 'contactable_type'];

    public function contactable() {
        return $this->morphTo();
    }

    public function type() {
        return $this->belongsTo('App\Models\ContactType', 'contact_type_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    public function contactable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visibility extends Model
{
    protected $table = 'visibilities';

    protected $fillable = [
        'name',
        ];

    public function articles() {
        return $this->hasMany('App\Models\Article');
    }

    public function events() {
        return $this->hasMany('App\Models\Event');
    }
}

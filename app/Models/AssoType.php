<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssoType extends Model
{
    protected $fillable = [
        'name', 'description',
    ];

    public function asso() {
        return $this->hasMany('App\Models\Asso');
    }

    protected $table = 'assos_types';
}

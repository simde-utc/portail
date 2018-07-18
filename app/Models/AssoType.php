<?php

namespace App\Models;

class AssoType extends Model
{
    protected $table = 'assos_types';

    protected $fillable = [
        'type', 'name',
    ];

    protected $must = [
        'type', 'name',
    ];

    public function asso() {
        return $this->hasMany(Asso::class);
    }
}

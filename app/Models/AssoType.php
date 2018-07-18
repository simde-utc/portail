<?php

namespace App\Models;

class AssoType extends Model // TODO A changer en type et name
{
    protected $table = 'assos_types';

    protected $fillable = [
        'name', 'description',
    ];

    public function asso() {
        return $this->hasMany(Asso::class);
    }
}

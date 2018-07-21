<?php

namespace App\Models;

class Partner extends Model // TODO $must ?
{
    protected $table = 'partners';

    protected $fillable = [
        'name', 'description', 'image',
    ];
}

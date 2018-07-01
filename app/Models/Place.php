<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
		'name', 'adress', 'city', 'country', 'position',
	];

    public function locations() {
        return $this->hasMany(Location::class);
    }
}

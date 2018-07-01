<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
		'name', 'place_id', 'position',
	];

    public function place() {
        return $this->belongsTo(Place::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Location extends Model
{
    use SpatialTrait;

    protected $table = "places_locations";

    protected $fillable = [
		'name', 'place_id', 'position',
	];

    protected $spatialFields = [
        'position',
    ];

    public function place() {
        return $this->belongsTo(Place::class);
    }
}

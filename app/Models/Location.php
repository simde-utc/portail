<?php

namespace App\Models;

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

    protected $with = [
        'place'
    ];

    protected $hidden = [
        'place_id',
    ];

	public function hideData(array $params = []): Model {
		return $this; // TODO
	}

    public function place() {
        return $this->belongsTo(Place::class);
    }
}

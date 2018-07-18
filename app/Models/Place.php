<?php

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Place extends Model // TODO $must
{
    use SpatialTrait;

    protected $fillable = [
		'name', 'address', 'city', 'country', 'position',
	];

    protected $spatialFields = [
        'position',
    ];

    public function locations() {
        return $this->hasMany(Location::class);
    }
}

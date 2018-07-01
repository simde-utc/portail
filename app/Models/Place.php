<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Place extends Model
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

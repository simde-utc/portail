<?php
/**
 * Model corresponding to places.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

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

    protected $must = [
        'position',
    ];

    /**
     * Relation with the location.
     *
     * @return mixed
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}

<?php
/**
 * Modèle correspondant aux places.
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
     * Relation avec le lieu.
     *
     * @return mixed
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}

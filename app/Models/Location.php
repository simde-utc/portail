<?php
/**
 * Modèle correspondant aux lieux.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SpatialTrait, SoftDeletes;

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
        'place_id'
    ];

    protected $must = [
        'place', 'position',
    ];

    /**
     * Relation avec l'emplacement.
     *
     * @return mixed
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function getPositionLatitudeAttribute() {
        return $this->position->getLat();
    }

    public function getPositionLongitudeAttribute() {
        return $this->position->getLng();
    }
}

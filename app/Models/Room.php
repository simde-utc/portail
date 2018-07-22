<?php

namespace App\Models;

class Room extends Model // TODO$must ?
{
	protected $table = 'reservations_rooms';

	protected $fillable = [
		'location_id', 'asso_id',
	];

	public function location() {
		return $this->belongsTo(Location::class);
	}

    public function asso() {
        return $this->belongsTo(Asso::class);
    }
}

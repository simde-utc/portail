<?php

namespace App\Models;

class Reservation extends Model
{
    protected $fillable = [
        'room_id', 'reservation_type_id', 'event_id', 'description', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'confirmed_by_id', 'confirmed_by_type',
    ];

    protected $with = [
        'owned_by'
    ];

    protected $must = [
        'room_id', 'reservation_type_id', 'event_id', 'description', 'owned_by'
    ];

    public function created_by() {
        return $this->morphTo('created_by');
    }

    public function owned_by() {
        return $this->morphTo('owned_by');
    }

    public function confirmed_by() {
        return $this->morphTo('confirmed_by');
    }
}

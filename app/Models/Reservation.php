<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasOwnerSelection;

class Reservation extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection;

    protected $fillable = [
        'room_id', 'reservation_type_id', 'event_id', 'description', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'confirmed_by_id', 'confirmed_by_type',
    ];

    protected $hidden = [
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'confirmed_by_id', 'confirmed_by_type',
    ];

    protected $with = [
        'created_by', 'owned_by', 'confirmed_by',
    ];

    protected $must = [
        'room_id', 'reservation_type_id', 'event_id', 'description', 'owned_by', 'confirmed_by',
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

<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasOwnerSelection;

class Reservation extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection;

    protected $table = 'rooms_reservations';

    protected $fillable = [
      'room_id', 'reservation_type_id', 'event_id', 'description', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'validated_by_id', 'validated_by_type',
    ];

    protected $hidden = [
      'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'validated_by_id', 'validated_by_type', 'reservation_type_id', 'event_id', 'room_id',
    ];

    protected $with = [
      'created_by', 'owned_by', 'validated_by', 'type', 'event', 'room',
    ];

    protected $must = [
      'room_id', 'reservation_type_id', 'event_id', 'description', 'owned_by', 'validated_by', 'type', 'event',
    ];

    protected static function boot() {
  		parent::boot();

  		self::updating(function ($model) {
  			$model->event->update([
  				'owned_by_id' => $model->owned_by_id,
  				'owned_by_type' => $model->owned_by_type,
  			]);
  		});

      self::deleting(function ($model) {
        $model->event->softDelete();
      });
  	}

  	public function type() {
  		return $this->belongsTo(ReservationType::class, 'reservation_type_id');
  	}

    public function room() {
      return $this->belongsTo(Room::class);
    }

    public function event() {
      return $this->belongsTo(Event::class);
    }

    public function created_by() {
      return $this->morphTo('created_by');
    }

    public function owned_by() {
      return $this->morphTo('owned_by');
    }

    public function validated_by() {
      return $this->morphTo('validated_by');
    }
}

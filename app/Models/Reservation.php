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
      'room_id', 'reservation_type_id', 'event_id', 'description', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'validated_by_id', 'validated_by_type',
    ];

    protected $hidden = [
      'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'validated_by_id', 'validated_by_type', 'reservation_type_id', 'event_id',
    ];

    protected $with = [
      'created_by', 'owned_by', 'validated_by', 'type', 'event'
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
      })
  	}

    public static function create(array $attributes = []) {
      if (isset($attributes['event'])) {
        $eventAttributes = $attributes['event'];
        $room = Room::find($attributes['room_id']);

        $eventAttributes['location_id'] = $room->location->id;
        $eventAttributes['created_by_id'] = $attributes['created_by_id'] ?? null;
        $eventAttributes['created_by_type'] = $attributes['created_by_type'] ?? null;
        $eventAttributes['owned_by_id'] = $attributes['owned_by_id'] ?? null;
        $eventAttributes['owned_by_type'] = $attributes['owned_by_type'] ?? null;

        $event = Event::create($eventAttributes);

        $attributes['event_id'] = $event->id;
        unset($attributes['event']);
      }

      return static::query()->create($attributes);
    }

  	public function type() {
  		return $this->belongsTo(ReservationType::class, 'reservations_type_id');
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

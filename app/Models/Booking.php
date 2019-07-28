<?php
/**
 * Model corresponding to bookings.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasOwnerSelection;

class Booking extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection;

    protected $table = 'rooms_bookings';

    protected $fillable = [
        'room_id', 'type_id', 'event_id', 'description', 'created_by_id', 'created_by_type', 'owned_by_id',
        'owned_by_type', 'validated_by_id', 'validated_by_type',
    ];

    protected $hidden = [
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'validated_by_id', 'validated_by_type',
        'type_id', 'event_id', 'room_id',
    ];

    protected $with = [
        'created_by', 'owned_by', 'validated_by', 'type', 'event', 'room',
    ];

    protected $must = [
        'room_id', 'type_id', 'event_id', 'description', 'owned_by', 'validated_by', 'type', 'event',
    ];

    /**
     * Launched at the model creation.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::updating(function ($model) {
            $model->event->update([
                'owned_by_id' => $model->owned_by_id,
                'owned_by_type' => $model->owned_by_type,
            ]);
        });

        self::deleting(function ($model) {
            $model->event->delete();
        });
    }

    /**
     * Relation with the booking type.
     *
     * @return mixed
     */
    public function type()
    {
        return $this->belongsTo(BookingType::class, 'type_id');
    }

    /**
     * Relation with the room.
     *
     * @return mixed
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relation with an event.
     *
     * @return mixed
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation with the creator.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo('created_by');
    }

    /**
     * Relation with the owner.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo('owned_by');
    }

    /**
     * Relation with the validating person.
     *
     * @return mixed
     */
    public function validated_by()
    {
        return $this->morphTo('validated_by');
    }
}

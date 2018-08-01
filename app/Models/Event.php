<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;

class Event extends Model implements OwnableContract
{
    use HasMorphOwner;

    protected $fillable = [
        'name', 'location_id', 'visibility_id', 'begin_at', 'end_at', 'full_day', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
    ];

    protected $casts = [
        'full_day' => 'boolean',
    ];

    protected $hidden = [
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
    ];

    protected $with = [
        'created_by', 'owned_by', 'visibility', 'location', 'details'
    ];

	protected $withModelName = [
		'created_by', 'owned_by',
	];

    protected $must = [
        'begin_at', 'end_at', 'full_day',
    ];

    protected $selection = [
        'pagination' => null,
        'order' => 'latest',
        'month' => null,
        'week' => null,
        'day' => 'now',
    ];

    protected $order_by = 'begin_at';
    protected $begin_at = 'begin_at';
    protected $end_at = 'begin_at';

    public function created_by() {
        return $this->morphTo();
    }

    public function owned_by() {
        return $this->morphTo();
    }

	public function visibility() {
    	return $this->belongsTo(Visibility::class);
    }

    public function getParticipantsAttribute() {
        return $this->calendars->map(function ($calendar) {
            return $calendar->owned_by;
        })->filter(function ($owner) {
            return $owner instanceof User;
        });
    }

    public function calendars() {
        return $this->belongsToMany(Calendar::class, 'calendars_events')->withTimestamps();
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function details() {
        return $this->hasMany(EventDetail::class);
    }

	public function user() {
		return $this->morphTo(User::class, 'owned_by');
	}

	public function asso() {
		return $this->morphTo(Asso::class, 'owned_by');
	}

	public function client() {
		return $this->morphTo(Client::class, 'owned_by');
	}

	public function group() {
		return $this->morphTo(Group::class, 'owned_by');
	}
}

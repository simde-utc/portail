<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use Illuminate\Database\Eloquent\Model;
use App\Models\Calendar;
use App\Models\Visibility;
use App\Models\User;
use App\Models\Asso;
use App\Models\Group;

class Event extends Model implements OwnableContract
{
    use HasMorphOwner;

    protected $fillable = [
        'name', 'location_id', 'begin_at', 'end_at', 'full_day', 'owned_by_id', 'owned_by_type',
    ];

    protected $casts = [
        'full_day' => 'boolean',
    ];

    public function owned_by() {
        return $this->morphTo();
    }

    public function calendars() {
        return $this->hasMany(Calendars::class, 'calendars_events');
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

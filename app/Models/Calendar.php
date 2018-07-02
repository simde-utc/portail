<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Visibility;
use App\Models\User;
use App\Models\Asso;
use App\Models\Group;

class Calendar extends Model implements OwnableContract
{
    use HasMorphOwner;

    protected $fillable = [
        'name', 'description', 'color', 'visibility_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
    ];

    public function owned_by() {
        return $this->morphTo();
    }

    public function events() {
        return $this->belongsToMany(Event::class, 'calendars_events')->withTimestamps();
    }

	public function visibility() {
    	return $this->hasOne(Visibility::class);
    }

	public function user() {
		return $this->morphTo(User::class, 'owned_by');
	}

    public function followers() {
        return $this->belongsToMany(User::class, 'calendars_followers')->withTimestamps();
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

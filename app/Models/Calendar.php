<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\Visibility;
use App\Models\User;
use App\Models\Asso;
use App\Models\Group;

class Calendar extends Model
{
    protected $fillable = [
        'name', 'description', 'color', 'visibility_id', 'created_by', 'owned_by',
    ];

    public function events() {
        return $this->hasMany(Event::class, 'calendars_events');
    }

	public function visibility() {
    	return $this->hasOne(Visibility::class);
    }

	public function users() {
		return $this->morphTo(User::class, 'owned_by');
	}

	public function assos() {
		return $this->morphTo(Asso::class, 'owned_by');
	}

	public function groups() {
		return $this->morphTo(Group::class, 'owned_by');
	}
}

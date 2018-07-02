<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarFollower extends Model
{
    protected $table = 'calendars_followers';

    protected $fillable = [
        'user_id', 'calendar_id',
    ];

	public function user() {
		return $this->belongsTo(User::class);
	}

	public function calendar() {
		return $this->belongsTo(Calendar::class);
	}
}

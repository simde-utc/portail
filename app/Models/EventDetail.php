<?php

namespace App\Models;

use App\Traits\Model\HasKeyValue;
use App\Models\Event;

class EventDetail extends Model // TODO $must ?
{
	use HasKeyValue;

	protected $table = 'events_details';

	protected $primaryKey = [
		'event_id', 'key'
	];

	protected $fillable = [
		'event_id', 'key', 'value', 'type',
	];

	public function getKeyName() {
		return 'event_id';
	}

	public function event() {
		$this->belongsTo(Event::class);
	}
}

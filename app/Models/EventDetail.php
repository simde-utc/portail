<?php

namespace App\Models;

use App\Traits\Model\HasKeyValue;
use App\Models\Event;

class EventDetail extends Model // TODO $must ?
{
	use HasKeyValue;

	public $incrementing = false; // L'id n'est pas autoincrementé

	protected $table = 'events_details';

	protected $primaryKey = [
		'event_id', 'key'
	];

	protected $fillable = [
		'event_id', 'key', 'value', 'type',
	];

	public function event() {
		$this->belongsTo(Event::class);
	}
}

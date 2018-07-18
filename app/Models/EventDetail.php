<?php

namespace App\Models;

use App\Traits\Model\HasKeyValue;
use App\Models\Event;

class EventDetail extends Model
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
	
	public function hideData(array $params = []): Model {
		return $this; // TODO
	}

	public function event() {
		$this->belongsTo(Event::class);
	}
}

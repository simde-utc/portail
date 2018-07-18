<?php

namespace App\Models;

class ReservationType extends Model
{
    protected $table = 'reservations_types';
    // TODO !

	public function hideData(array $params = []): Model {
		return $this; // TODO
	}
}

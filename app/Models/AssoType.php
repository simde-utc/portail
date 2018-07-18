<?php

namespace App\Models;

class AssoType extends Model // TODO A changer en type name
{
    protected $table = 'assos_types';

    protected $fillable = [
        'name', 'description',
    ];

	public function hideData(array $params = []): Model {
		return $this; // TODO
	}

    public function asso() {
        return $this->hasMany('App\Models\Asso');
    }
}

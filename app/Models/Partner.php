<?php

namespace App\Models;



class Partner extends Model
{
    protected $table = 'partners';

    protected $fillable = [
        'name', 'description', 'image',
    ];
    
	public function hideData(array $params = []): Model {
		return $this; // TODO
	}
}

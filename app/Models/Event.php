<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Visibility\BaseVisibility;


class Event extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'title', 'description', 'image', 'from', 'to', 'visibility_id', 'place',
    ];

	public function users() {
			return $this->belongsToMany('App\Models\User');
	}

	public function assos() {
			return $this->belongsToMany('App\Models\Asso');
	}

	public function visibility() {
    	return $this->hasOne('App\Models\Visibility');
    }
}

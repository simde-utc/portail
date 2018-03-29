<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'user_id', 'icon_id', 'visibility_id', 'is_active',
    ];

  	protected $casts = [
  		'is_active' => 'boolean',
  	];

    protected $dates = [
        'deleted_at'
    ];

    public function visibility() {
    	return $this->hasOne('App\Models\Visibility');
    }

  	public function members() {
  		return $this->belongsToMany('App\Models\User', 'groups_members');
  	}
}

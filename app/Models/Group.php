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

    // On les cache car on récupère directement le user et la vibility dans le controller
    protected $hidden = [
        'user_id', 'visibility_id'
    ];

    public function owner() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function visibility() {
    	return $this->belongsTo('App\Models\Visibility', 'visibility_id');
    }

    public function members() {
        return $this->belongsToMany('App\Models\User', 'groups_members')->withPivot('created_at');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasRoles, SoftDeletes;

    public static function boot() {
        static::created(function ($model) {
            $model->assignRole('group admin', [
				'user_id' => $model->user_id,
				'validated_by' => $model->user_id,
				'semester_id' => null,
			], true);
        });
    }

    protected $fillable = [
        'name', 'user_id', 'icon_id', 'visibility_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'deleted_at'
    ];

    // On les caches car on récupère directement le user et la vibility dans le controller
    protected $hidden = [
        'user_id', 'visibility_id'
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function visibility() {
    	return $this->belongsTo(Visibility::class, 'visibility_id');
    }
}

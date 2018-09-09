<?php

namespace App\Models;

use App\Models\Visibility;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasStages;

class Visibility extends Model
{
    use HasStages;

    protected $table = 'visibilities';

    protected $fillable = [
		'type', 'name', 'parent_id'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];

    protected $must = [
        'type',
    ];

    protected $selection = [
        'paginate' => [],
        'order' => [],
        'filter' => [],
    ];

	public static function findByType($type) {
		return static::where('type', $type)->first();
	}

    public function parent() {
        return $this->belongsTo(Visibility::class, 'parent_id');
    }

    public function children() {
        return $this->hasMany(Visibility::class, 'parent_id');
    }

    public function articles() {
        return $this->hasMany('App\Models\Article');
    }

    public function events() {
        return $this->hasMany('App\Models\Event');
    }
}

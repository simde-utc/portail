<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Visibility;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Visibility extends Model
{
    protected $table = 'visibilities';

    protected $fillable = [
		'type', 'name', 'parent_id'
	];

	protected $hidden = [
		'created_at', 'updated_at'
	];

	public static function findByType($type) {
		return static::where('type', $type)->first();
	}

	public function childs(): BelongsToMany {
		return $this->belongsToMany(Visibility::class, 'visibilities_parents', 'visibility_id', 'parent_id');
	}

	public function parents(): BelongsToMany {
		return $this->belongsToMany(Visibility::class, 'visibilities_parents', 'parent_id', 'visibility_id');
	}

    public function articles() {
        return $this->hasMany('App\Models\Article');
    }

    public function events() {
        return $this->hasMany('App\Models\Event');
    }

    // TODO les liens vers les modèles
    // TODO générer isVisibible($user_id) en fonction des droits de la personne de visibilité (passer par un service ?)
}

<?php

namespace App\Models;

use App\Models\Visibility;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasStages;

class Visibility extends Model // TODO $must?
{
    use HasStages;

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

    public static function getTopStage(array $data = [], $with = []) {
        $tableName = (new static)->getTable();
        $model = static::whereNull('parent_id')->with($with);

        foreach ($data as $key => $value) {
            if (!\Schema::hasColumn($tableName, $key))
                throw new PortailException('L\'attribut '.$key.' n\'existe pas');

            $model = $model->where($key, $value);
        }

        return collect()->push($model->first());
    }
}

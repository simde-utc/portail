<?php

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasOwnerSelection;

class Room extends Model implements OwnableContract
{
  use HasMorphOwner, HasCreatorSelection, HasOwnerSelection;

	protected $fillable = [
		'location_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id', 'calendar_id', 'capacity',
	];

	protected static function boot() {
		parent::boot();

    self::creating(function ($model) {
			if (!$model->calendar_id) {
				$model->calendar_id = Calendar::create([
					'name' => 'Réservation',
					'description' => 'Planning de réservation',
					'visibility_id' => $model->visibility_id,
					'created_by_id' => $model->created_by_id,
					'created_by_type' => $model->created_by_type,
					'owned_by_id' => $model->owned_by_id,
					'owned_by_type' => $model->owned_by_type,
				])->id;
			}
		});

		self::updated(function ($model) {
			$model->calendar->update([
				'visibility_id' => $model->visibility_id,
				'owned_by_id' => $model->owned_by_id,
				'owned_by_type' => $model->owned_by_type,
			]);
		});
	}

	protected $hidden = [
		'location_id', 'calendar_id', 'created_by_id', 'created_by_type', 'owned_by_type', 'owned_by_type', 'visibility_id',
	];

	protected $with = [
		'location', 'calendar', 'created_by', 'owned_by', 'visibility',
	];

	protected $must = [
		'location', 'owned_by', 'calendar', 'capacity',
	];

	public function visibility() {
		return $this->belongsTo(visibility::class);
	}

	public function location() {
		return $this->belongsTo(Location::class);
	}

	public function calendar() {
		return $this->belongsTo(Calendar::class);
	}

  public function created_by() {
    return $this->morphTo('created_by');
  }

  public function owned_by() {
    return $this->morphTo('owned_by');
  }
}

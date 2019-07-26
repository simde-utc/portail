<?php
/**
 * Model corresponding to rooms.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\{
    HasCreatorSelection, HasOwnerSelection, HasVisibilitySelection
};

class Room extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection, HasVisibilitySelection;

    protected $fillable = [
        'location_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id', 'calendar_id',
        'capacity',
    ];

    protected $hidden = [
        'location_id', 'calendar_id', 'created_by_id', 'created_by_type', 'owned_by_type', 'owned_by_type', 'visibility_id',
    ];

    protected $with = [
        'location', 'calendar', 'created_by', 'owned_by', 'visibility',
    ];

    protected $must = [
        'location', 'owned_by', 'calendar', 'capacity', 'visibility'
    ];

    protected $selection = [
        'visibilities' => '*',
        'paginate' => null,
        'order' => null,
        'creator' => null,
        'owner' => null,
        'filter' => [],
    ];

    /**
     * Called at the model creation.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (!$model->calendar_id) {
                $model->calendar_id = Calendar::create([
                    'name' => 'Réservation - '.(Location::find($model->location_id)->name),
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

    /**
     * Specific scope to have only private resources.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');
        $user = $this->getUserForVisibility();

        if ($user) {
            $asso_ids = $user->currentJoinedAssos()->pluck('id')->toArray();

            return $query->where('visibility_id', $visibility->id)->where(function ($subQuery) use ($user, $asso_ids) {
                return $subQuery->where(function ($subSubQuery) use ($user) {
                    return $subSubQuery->where('owned_by_type', User::class)->where('owned_by_id', $user->id);
                })->orWhere(function ($subSubQuery) use ($asso_ids) {
                    return $subSubQuery->where('owned_by_type', Asso::class)->whereIn('owned_by_id', $asso_ids);
                })->orWhere(function ($subSubQuery) use ($asso_ids) {
                    return $subSubQuery->where('owned_by_type', Client::class)
                        ->whereIn('owned_by_id', Client::whereIn('asso_id', $asso_ids)->pluck('id')->toArray());
                })->orWhere(function ($subSubQuery) use ($user) {
                    return $subSubQuery->where('owned_by_type', Group::class)
                        ->whereIn('owned_by_id', $user->groups()->pluck('id')->toArray());
                });
            });
        }
    }

    /**
     * Return the room name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->location->name;
    }

    /**
     * Relation with the visibility.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Relation with the location.
     *
     * @return mixed
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relation with the calendar.
     *
     * @return mixed
     */
    public function calendar()
    {
        return $this->belongsTo(Calendar::class);
    }

    /**
     * Relation with the creator.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo('created_by');
    }

    /**
     * Relation with the owner.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo('owned_by');
    }

    /**
     * Relation with bookings.
     *
     * @return mixed
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}

<?php
/**
 * Model corresponding to events.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasVisibilitySelection;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model implements OwnableContract
{
    use HasMorphOwner, HasVisibilitySelection;

    protected $fillable = [
        'name', 'location_id', 'visibility_id', 'begin_at', 'end_at', 'full_day', 'created_by_id', 'created_by_type',
        'owned_by_id', 'owned_by_type',
    ];

    protected $casts = [
        'full_day' => 'boolean',
    ];

    protected $hidden = [
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'location_id',
    ];

    protected $with = [
        'created_by', 'owned_by', 'visibility', 'location'
    ];

    protected $withModelName = [
        'created_by', 'owned_by',
    ];

    protected $must = [
        'begin_at', 'end_at', 'full_day', 'location', 'owned_by', 'visibility'
    ];

    protected $selection = [
        'paginate' => null,
        'order' => [
            'default' => 'latest',
            'columns' => [
                'date' => 'begin_at',
            ],
        ],
        'month' => [
            'columns' => [
                'begin' => 'begin_at',
                'end' => 'begin_at',
            ],
        ],
        'week' => [
            'columns' => [
                'begin' => 'begin_at',
                'end' => 'begin_at',
            ],
        ],
        'day' => [
            'columns' => [
                'begin' => 'begin_at',
                'end' => 'begin_at',
            ],
        ],
        'filter' => [],
    ];

    /**
     * Specific scope to have only the private resources.
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
     * Relation with the creator.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo();
    }

    /**
     * Relation with the event owner.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo();
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
     * Create dynamically the 'participants' attribute.
     *
     * @return array
     */
    public function getParticipantsAttribute()
    {
        return $this->calendars->map(function ($calendar) {
            return $calendar->owned_by;
        })->filter(function ($owner) {
            return $owner instanceof User;
        });
    }

    /**
     * Relation with calendars.
     *
     * @return mixed
     */
    public function calendars()
    {
        return $this->belongsToMany(Calendar::class, 'calendars_events')->withTimestamps();
    }

    /**
     * Relation with location.
     *
     * @return mixed
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relation with details.
     *
     * @return mixed
     */
    public function details()
    {
        return $this->hasMany(EventDetail::class);
    }

    /**
     * Relation with the user.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->morphTo(User::class, 'owned_by');
    }

    /**
     * Relation with the association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->morphTo(Asso::class, 'owned_by');
    }

    /**
     * Relation with the OAuth client.
     *
     * @return mixed
     */
    public function client()
    {
        return $this->morphTo(Client::class, 'owned_by');
    }

    /**
     * Relation with the group.
     *
     * @return mixed
     */
    public function group()
    {
        return $this->morphTo(Group::class, 'owned_by');
    }

    /**
     * Overload the mothod to cache sub-data.
     *
     * @param boolean $addSubModelName
     * @return mixed
     */
    public function hideSubData(bool $addSubModelName=false)
    {
        $this->details = $this->details()->allToArray();

        return parent::hideSubData($addSubModelName);
    }
}

<?php
/**
 * Modèle correspondant aux événements.
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

class Event extends Model implements OwnableContract
{
    use HasMorphOwner;

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
        'begin_at', 'end_at', 'full_day', 'location'
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
     * Relation avec la personne créatrive.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo();
    }

    /**
     * Relation avec la personne possédant l'événement.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo();
    }

    /**
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Crée dynamiquement l'attribut participants
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
     * Relation avec les calendriers.
     *
     * @return mixed
     */
    public function calendars()
    {
        return $this->belongsToMany(Calendar::class, 'calendars_events')->withTimestamps();
    }

    /**
     * Relation avec le lieu.
     *
     * @return mixed
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relation avec les détails.
     *
     * @return mixed
     */
    public function details()
    {
        return $this->hasMany(EventDetail::class);
    }

    /**
     * Relation avec l'utilisateur.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->morphTo(User::class, 'owned_by');
    }

    /**
     * Relation avec l'association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->morphTo(Asso::class, 'owned_by');
    }

    /**
     * Relation avec le client oauth.
     *
     * @return mixed
     */
    public function client()
    {
        return $this->morphTo(Client::class, 'owned_by');
    }

    /**
     * Relation avec le groupe.
     *
     * @return mixed
     */
    public function group()
    {
        return $this->morphTo(Group::class, 'owned_by');
    }

    /**
     * Surcharge de la méthode pour cacher les sous-données.
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

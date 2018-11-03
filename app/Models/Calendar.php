<?php
/**
 * Modèle correspondant aux calendriers.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasOwnerSelection;

class Calendar extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection;

    protected $fillable = [
        'name', 'description', 'color', 'visibility_id', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
    ];

    protected $hidden = [
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id',
    ];

    protected $with = [
        'created_by', 'owned_by', 'visibility',
    ];

    protected $withModelName = [
        'created_by', 'owned_by',
    ];

    protected $must = [
        'description', 'color', 'owned_by',
    ];

    protected $selection = [
        'paginate' => null,
        'order' => null,
        'owner' => null,
        'creator' => null,
        'filter' => [],
    ];

    /**
     * Relation avec les évènements.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'calendars_events')->withTimestamps();
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
     * Relation avec l'utilisateur.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->morphTo(User::class, 'owned_by');
    }

    /**
     * Relation avec les suiveurs.
     *
     * @return mixed
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'calendars_followers')->withTimestamps();
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
     * Indique si le calendrier est accessible.
     * Seule la personne qui possède le calendrier peut le voir.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarAccessibleBy(string $user_id): bool
    {
        return $this->owned_by->isCalendarAccessibleBy($user_id);
    }

    /**
     * Indique si le calendrier est gérable.
     * Seule la personne qui possède le calendrier peut le modifier.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool
    {
        return $this->owned_by->isCalendarManageableBy($user_id);
    }
}

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

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\{
    HasCreatorSelection, HasOwnerSelection, HasVisibilitySelection
};

class Calendar extends Model implements OwnableContract
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection, HasVisibilitySelection {
        HasVisibilitySelection::prepareVisibilitiesQuery as private prepareVisibilitiesQueryFromTrait;
    }

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
        'description', 'color', 'owned_by', 'visibility'
    ];

    protected $selection = [
        'visibilities' => '*',
        'paginate' => null,
        'order' => null,
        'owner' => null,
        'creator' => null,
        'filter' => [],
    ];

    /**
     * On défini une couleur au hasard si elle n'est pas définie.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->color) {
                $model->color = '#';

                for ($i = 0; $i < 6; $i++) {
                    $model->color .= '0123456789ABCDEF'[random_int(0, 15)];
                }
            }
        });
    }

    /**
     * Prépare la requête pour chaque visibilité.
     *
     * @param  Builder $subQuery
     * @return Builder
     */
    protected function prepareVisibilitiesQuery(Builder $subQuery)
    {
        $subQuery = $this->prepareVisibilitiesQueryFromTrait($subQuery);

        if ($user = $this->getUserForVisibility()) {
            $subQuery = $subQuery->orWhereIn('id', $user->followedCalendars()->pluck('id')->toArray());
        }

        return $subQuery;
    }

    /**
     * Scope spécifique pour n'avoir que les ressources privées.
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

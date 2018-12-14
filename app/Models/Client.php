<?php
/**
 * Modèle correspondant aux clients oauth.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Laravel\Passport\Client as PassportClient;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveEvents;
use App\Interfaces\Model\CanHaveArticles;
use App\Interfaces\Model\CanNotify;
use App\Traits\Model\HasHiddenData;
use App\Traits\Model\HasUuid;
use NastuzziSamy\Laravel\Traits\HasSelection;

class Client extends PassportClient implements CanHaveCalendars, CanHaveEvents, CanHaveArticles, CanNotify
{
    use HasHiddenData, HasSelection, HasUuid;

    public $incrementing = false;

    protected $fillable = [
        'user_id', 'asso_id', 'name', 'secret', 'redirect', 'targeted_types',
        'personal_access_client', 'password_client', 'revoked', 'created_at', 'updated_at', 'scopes'
    ];

    protected $selection = [
        'paginate' => null,
        'filter' => [],
    ];

    /**
     * Relation avec l'utilisateur.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->belongsTo(Asso::class);
    }

    /**
     * Relation avec les calendriers.
     *
     * @return mixed
     */
    public function calendars()
    {
        return $this->morphMany(Calendar::class, 'owned_by');
    }

    /**
     * Relation avec les évènements.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'owned_by');
    }

    /**
     * Relation avec les articles.
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->morphMany(Article::class, 'owned_by');
    }

    /**
     * Indique si le calendrier est accessible.
     * Le calendrier privé est visible par tous les membres.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarAccessibleBy(string $user_id): bool
    {
        return $this->asso()->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si le calendrier est gérable.
     * Le calendrier privé est modifiable uniquement par les développeurs.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool
    {
        return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
    }

    /**
     * Indique si l'évènement est accessible.
     * L'évènement privé est visible par tous les membres.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventAccessibleBy(string $user_id): bool
    {
        return $this->asso()->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si l'évènement est gérable.
     * L'évènement privé est modifiable uniquement par les développeurs.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool
    {
        return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
    }

    /**
     * Indique si l'article est accessible.
     * L'article privé est visible par tous les membres.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isArticleAccessibleBy(string $user_id): bool
    {
        return $this->asso()->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si l'évènement est gérable.
     * L'évènement privé est modifiable uniquement par les développeurs.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool
    {
        return $this->asso()->hasOneRole('developer', ['user_id' => $user_id]);
    }
}

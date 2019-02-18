<?php
/**
 * Modèle correspondant aux associations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Contracts\Ownership\CanBeOwner;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Model\{
    HasMembers, HasStages, HasDeletedSelection
};
use App\Interfaces\Model\{
    CanHaveContacts, CanHaveEvents, CanHaveCalendars, CanHaveArticles, CanHaveRooms,
    CanHaveReservations, CanNotify, CanHaveRoles, CanHavePermissions, CanComment
};
use Illuminate\Support\Collection;
use App\Exceptions\PortailException;

class Asso extends Model implements CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents, CanHaveArticles,
	CanNotify, CanHaveRooms, CanHaveReservations, CanHaveRoles, CanHavePermissions, CanComment
{
    use HasStages, HasMembers, SoftDeletes, HasDeletedSelection {
        HasMembers::members as membersAndFollowers;
        HasMembers::currentMembers as currentMembersAndFollowers;
        HasMembers::joiners as protected joinersFromHasMembers;
        HasMembers::currentJoiners as currentJoinersFromHasMembers;
        HasMembers::getUserRoles as getUsersRolesInThisAssociation;
    }

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'name', 'shortname', 'login', 'image', 'description', 'type_id', 'parent_id',
    ];

    protected $hidden = [
        'type_id', 'parent_id',
    ];

    protected $with = [
        'type',
    ];

    protected $optional = [
        'children', 'parent'
    ];

    protected $must = [
        'name', 'shortname', 'login', 'image', 'deleted_at',
    ];

    // Children dans le cas où on affiche en mode étagé.
    protected $selection = [
        'order' => [
            'default' => 'oldest',
            'columns' => [
                'name' => 'shortname',
            ],
        ],
        'deleted' => 'without',
        'filter' => [],
        'stage' => null,
        'stages' => null,
    ];

    protected $roleRelationTable = 'assos_members';

    /**
     * Appelé à la création du modèle.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // On crée automatiquement des moyens de contacts !
            $model->contacts()->create([
                'name' => 'Adresse email',
                'value' => $model->login.'@assos.utc.fr',
                'type_id' => ContactType::where('name', 'Adresse email')->first()->id,
                'visibility_id' => Visibility::findByType('public')->id,
            ]);

            $model->contacts()->create([
                'name' => 'Site Web',
                'value' => 'https://assos.utc.fr/'.$model->login.'/',
                'type_id' => ContactType::where('name', 'Url')->first()->id,
                'visibility_id' => Visibility::findByType('public')->id,
            ]);
        });
    }

    /**
     * Retrouve une association par son login.
     *
     * @param  mixed  $query
     * @param  string $login
     * @return mixed
     */
    public function scopeFindByLogin($query, string $login)
    {
        return $query->where('login', $login)->first();
    }

    /**
     * Relation avec le type d'association.
     *
     * @return mixed
     */
    public function type()
    {
        return $this->belongsTo(AssoType::class, 'type_id');
    }

    /**
     * Relation avec l'association parent.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->hasOne(Asso::class, 'id', 'parent_id');
    }

    /**
     * Relation avec les associations enfants.
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Asso::class, 'parent_id', 'id');
    }

    /**
     * Relation avec les accès de l'association.
     *
     * @return mixed
     */
    public function access()
    {
        return $this->hasMany(AssoAccess::class);
    }

    /**
     * Relation avec les membres de l'association.
     *
     * @return mixed
     */
    public function members()
    {
        return $this->membersAndFollowers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation avec les membres du semestre actuel de l'association.
     *
     * @return mixed
     */
    public function currentMembers()
    {
        return $this->currentMembersAndFollowers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation avec les membres en attente de validation de l'association.
     *
     * @return mixed
     */
    public function joiners()
    {
        return $this->joinersFromHasMembers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation avec les membres en attente de validation du semestre actuel de l'association.
     *
     * @return mixed
     */
    public function currentJoiners()
    {
        return $this->currentJoinersFromHasMembers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation avec les suiveurs de l'association.
     *
     * @return mixed
     */
    public function followers()
    {
        return $this->membersAndFollowers()->wherePivot('role_id', null);
    }

    /**
     * Relation avec les suiveurs du semestre actuel de l'association.
     *
     * @return mixed
     */
    public function currentFollowers()
    {
        return $this->currentMembersAndFollowers()->wherePivot('role_id', null);
    }

    /**
     * Récupération des rôles dans l'association.
     *
     * @param  string $user_id
     * @param  string $semester_id
     * @return Collection
     */
    public function getUserRoles(string $user_id=null, string $semester_id=null)
    {
        $parent_id = $this->parent_id;
        $roles = $this->getUsersRolesInThisAssociation($user_id, $semester_id);

        while ($parent_id) {
            $asso = static::find($parent_id);

            foreach ($asso->getUsersRolesInThisAssociation($user_id, $semester_id) as $role) {
                $roles->push($role);

                $roles = $roles->merge($role->allChildren());
                $role->makeHidden('children');
            }

            $parent_id = $asso->parent_id;
        }

        return $roles->unique('id');
    }

    /**
     * Donne le dernier utilisateur avec un rôle.
     *
     * @param  mixed $role
     * @return User|null
     */
    public function getLastUserWithRole($role)
    {
        return $this->members()->wherePivot('role_id',
        	Role::getRole($role, $this)->id)->orderBy('semester_id', 'DESC'
        )->first();
    }

    /**
     * Indique si un rôle est affichable ou non.
     * On affiche toujours les rôles des membres, ce n'est pas un secret.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool
    {
        return true;
    }

    /**
     * Indique si un rôle est modifiable ou non.
     * Un rôle est modifiable uniquement par un membre ayant le droit.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->hasOnePermission('role', [
                'user_id' => $user_id,
            ]);
        } else {
            return User::find($user_id)->hasOnePermission('role');
        }
    }

    /**
     * Indique si une permission est affichable ou non.
     * On affiche toujours les permissions des membres, ce n'est pas un secret.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool
    {
        return true;
    }

    /**
     * Indique si une permission est gérable ou non.
     * Une permission est modifiable uniquement par un membre ayant la permission.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->hasOnePermission('permission', [
                'user_id' => $user_id,
            ]);
        } else {
            return User::find($user_id)->hasOnePermission('permission');
        }
    }

    /**
     * Relation avec les moyens de contacts de l'association.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'owned_by');
    }

    /**
     * Indique si le moyen de contact est accessible.
     * Affichable uniquement pour les membres (données privées).
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si le moyen de contact est gérable.
     * Modifiable uniquement par un membre ayant la permission.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('contact', [
            'user_id' => $user_id,
        ]);
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
     * Indique si le calendrier est accessible.
     * Seulement les membres peuvent voir les calendriers privées.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si le calendrier est gérable.
     * Seulement les membres ayant la permission peuvent modifier les calendriers privées.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('calendar', [
            'user_id' => $user_id,
        ]);
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
     * Indique si un évènement est accessible.
     * Seulement les membres peuvent voir les évènements privés.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si un évènement est gérable.
     * Seulement les membres ayant la permission peuvent modifier les évènements privés.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('event', [
            'user_id' => $user_id,
        ]);
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
     * Indique si un article est accessible.
     * Seulement les membres peuvent voir les articles privés.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isArticleAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si un article est gérable.
     * Seulement les membres peuvent modifier les articles privés.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('article', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Relation avec les salles.
     *
     * @return mixed
     */
    public function rooms()
    {
        return $this->morphMany(Room::class, 'owned_by');
    }

    /**
     * Indique si une salle est accessible.
     * Seulement les membres peuvent voir les salles privées.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoomAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si une salle est gérable.
     * Seulement les membres peuvent modifier les salles privées.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoomManageableBy(string $user_id): bool
    {
        return User::find($user_id)->hasOnePermission('room');
    }

    /**
     * Indique si la salle est réservable.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function isRoomReservableBy(\Illuminate\Database\Eloquent\Model $model): bool
    {
        if (!($model instanceof Asso)) {
            throw new PortailException('Seules les associations peuvent réserver une salle appartenant à une association', 503);
        }

        // On regarde si l'asso est un enfant de celle possédant la salle (ex: Picsart peut réserver du PAE).
        $toMatch = $model;
        while ($toMatch) {
            if ($toMatch->id === $this->id) {
                return true;
            }

            $toMatch = $toMatch->parent;
        }

        // Correspond aux assos parents.
        return $this->isReservationValidableBy($model);
    }

    /**
     * Relation avec les réservations.
     *
     * @return mixed
     */
    public function reservations()
    {
        return $this->morphMany(Reservation::class, 'owned_by');
    }

    /**
     * Indique si une réservation est accessible.
     * Seulement les membres peuvent voir les réservations privées.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isReservationAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indique si une réservation est gérable.
     * Seulement les membres peuvent modifier les réservations privées.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isReservationManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('reservation', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Indique si une réservation est validable.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function isReservationValidableBy(\Illuminate\Database\Eloquent\Model $model): bool
    {
        if ($model instanceof Asso) {
            // On regarde si l'asso possédant la salle est un enfant de celle qui fait la demande (ex: BDE à le droit sur PAE).
            $toMatch = $this;
            while ($toMatch) {
                if ($toMatch->id === $model->id) {
                    return true;
                }

                $toMatch = $toMatch->parent;
            }

            return false;
        } else if ($model instanceof User) {
            return $this->hasOnePermission('reservation', [
                'user_id' => $model->id,
            ]);
        } else if ($model instanceof Client) {
            return $model->asso->id === $this->id;
        } else {
            throw new PortailException('Seules les utilisateurs,
				associations et clients peuvent valider une salle appartenant à une association', 503);
        }
    }

    /**
     * Indique si un commentaire est rédigeable.
     * Les commentaires écrits par une asso se font uniquement par les gens pouvant en rédiger.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentWritableBy(string $user_id): bool
    {
        return $this->hasOnePermission('comment', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Indique si un commentaire est modifiable.
     * Les commentaires écrits par une asso se font uniquement par les gens pouvant en modifier.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentEditableBy(string $user_id): bool
    {
        return $this->isCommentWritableBy($user_id);
    }

    /**
     * Indique si un commentaire est supprimable.
     * Les commentaires écrits par une asso se font uniquement par les gens pouvant en supprimer.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool
    {
        return $this->isCommentEditableBy($user_id);
    }
}

<?php
/**
 * Model corresponding to associations.
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
use Illuminate\Notifications\Notifiable;
use App\Interfaces\Model\{
    CanHaveContacts, CanHaveEvents, CanHaveCalendars, CanHaveArticles, CanHaveRooms,
    CanHaveBookings, CanNotify, CanHaveRoles, CanHavePermissions, CanComment
};
use Illuminate\Support\Collection;
use App\Exceptions\PortailException;
use App\Pivots\AssoMember;

class Asso extends Model implements CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents, CanHaveArticles,
	CanNotify, CanHaveRooms, CanHaveBookings, CanHaveRoles, CanHavePermissions, CanComment
{
    use HasStages, HasMembers, SoftDeletes, HasDeletedSelection, Notifiable {
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

    // Children in the case of a staged mode display.
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
     * Called at the model creation.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // Automatic creation of contact means.
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

            // A calender for each association is created.
            $model->calendars()->create([
                'name' => 'Evénements',
                'description' => 'Calendrier regroupant les événements de l\'associations',
                'visibility_id' => Visibility::findByType('public')->id,
                'created_by_id' => $model->id,
                'created_by_type' => Asso::class,
            ]);
        });
    }

    /**
     * Find an association by login.
     *
     * @param  mixed  $query
     * @param  string $login
     * @return mixed
     */
    public function scopeFindByLogin($query, string $login)
    {
        $asso = $query->where('login', $login)->first();

        if ($asso) {
            return $asso;
        }

        throw new PortailException('Association non existante');
    }

    /**
     * Relation with association type.
     *
     * @return mixed
     */
    public function type()
    {
        return $this->belongsTo(AssoType::class, 'type_id');
    }

    /**
     * Relation with the association parent.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->hasOne(Asso::class, 'id', 'parent_id');
    }

    /**
     * Relation with the child associations.
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Asso::class, 'parent_id', 'id');
    }

    /**
     * Relation with the association accesses.
     *
     * @return mixed
     */
    public function access()
    {
        return $this->hasMany(AssoAccess::class);
    }

    /**
     * Relation with the association members.
     *
     * @return mixed
     */
    public function members()
    {
        return $this->membersAndFollowers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation with the association current members.
     *
     * @return mixed
     */
    public function currentMembers()
    {
        return $this->currentMembersAndFollowers()->wherePivot('role_id', '!=', null)->using(AssoMember::class);
    }

    /**
     * Relation with the association's members waitng for validation.
     *
     * @return mixed
     */
    public function joiners()
    {
        return $this->joinersFromHasMembers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation with the association's members waitng for validation during th ecurrent semester.
     *
     * @return mixed
     */
    public function currentJoiners()
    {
        return $this->currentJoinersFromHasMembers()->wherePivot('role_id', '!=', null);
    }

    /**
     * Relation with the followers of the association.
     *
     * @return mixed
     */
    public function followers()
    {
        return $this->membersAndFollowers()->wherePivot('role_id', null);
    }

    /**
     * Relation with the followers of the association during this semester.
     *
     * @return mixed
     */
    public function currentFollowers()
    {
        return $this->currentMembersAndFollowers()->wherePivot('role_id', null);
    }

    /**
     * Notify all association members.
     *
     * @param  mixed        $notification
     * @param  string|array $restrictToRoleIds
     * @return void
     */
    public function notifyMembers($notification, $restrictToRoleIds=null)
    {
        $members = $this->currentMembers();

        if ($restrictToRoleIds) {
            $members->wherePivotIn('role_id', (array) $restrictToRoleIds);
        }

        foreach ($members->get() as $member) {
            $member->notify($notification);
        }
    }

    /**
     * Return the notification email address.
     *
     * @param  mixed $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->contacts()->keyExistsInDB('CONTACT_EMAIL') ? $this->contacts()->valueOf('CONTACT_EMAIL') : null;
    }

    /**
     * Return the notification icon as creator.
     *
     * @param  Notification $notification
     * @return string
     */
    public function getNotificationIcon(Notification $notification)
    {
        return $this->image;
    }

    /**
     * Return the last user with a role.
     *
     * @param  mixed $role
     * @return User|null
     */
    public function getLastUserWithRole($role)
    {
        $members = $this->members()->wherePivot('role_id', Role::getRole($role, $this)->id)->get();

        $latestMember = null;
        foreach ($members as $member) {
            if (!$latestMember) {
                $latestMember = $member;
                continue;
            }

            $date = Semester::find($member->pivot->semester_id)->end_at;
            $lastDate = Semester::find($latestMember->pivot->semester_id)->end_at;

            if ($date > $lastDate) {
                $latestMember = $member;
            }
        }

        return $latestMember;
    }

    /**
     * Indicate if a role is accessible or not.
     * Members roles is not a secret. Always displaying them.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool
    {
        return true;
    }

    /**
     * Indicate if a role is manageable or not.
     * A role is manageable only by a member who has the permission.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool
    {
        return User::find($user_id)->hasOnePermission('role');
    }

    /**
     * Indicate if a permission is accessible or not.
     * Members permissions is not a secret. Always displaying them.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool
    {
        return true;
    }

    /**
     * Indicate if a permission is manageable or not.
     * A permission is manageable only by a member who has the permission.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool
    {
        return User::find($user_id)->hasOnePermission('permission');
    }

    /**
     * Relation with the associatin contact means.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'owned_by');
    }

    /**
     * Indicate if the contact mean is accessible.
     * Accessible only bu members (private data).
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indicate if the contact mean is manageable.
     * A contact mean is manageable only by a member who has the permission.
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
     * Relation with calendars.
     *
     * @return mixed
     */
    public function calendars()
    {
        return $this->morphMany(Calendar::class, 'owned_by');
    }

    /**
     * Indicate if the calendar is manageable.
     * A private calendar is manageable only by a member who has the permission.
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
     * Relation with events.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'owned_by');
    }

    /**
     * Indicate if an event is manageable.
     * An event is manageable only by a member who has the permission.
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
     * Relation with articles.
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->morphMany(Article::class, 'owned_by');
    }

    /**
     * Indicate if un article is manageable.
     * A private article is manageable only by a member.
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
     * Relation with rooms.
     *
     * @return mixed
     */
    public function rooms()
    {
        return $this->morphMany(Room::class, 'owned_by');
    }

    /**
     * Indicate if a room is manageable.
     * Private rooms are manageable only by a member.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoomManageableBy(string $user_id): bool
    {
        return User::find($user_id)->hasOnePermission('room');
    }

    /**
     * Indicate if the room the room is bookable.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function isRoomReservableBy(\Illuminate\Database\Eloquent\Model $model): bool
    {
        if (!($model instanceof Asso)) {
            throw new PortailException('Seules les associations peuvent réserver une salle appartenant à une association', 503);
        }

        // We check if this association is a child of the room owner (Example Picsart can book PAE's rooms).
        $toMatch = $model;
        while ($toMatch) {
            if ($toMatch->id === $this->id) {
                return true;
            }

            $toMatch = $toMatch->parent;
        }

        // Correspond to parents associations.
        return $this->isBookingValidableBy($model);
    }

    /**
     * Relation with bookings.
     *
     * @return mixed
     */
    public function bookings()
    {
        return $this->morphMany(Booking::class, 'owned_by');
    }

    /**
     * Indicate if a booking is accessible.
     * Private bookings are accessible only by a member.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isBookingAccessibleBy(string $user_id): bool
    {
        return $this->currentMembers()->wherePivot('user_id', $user_id)->exists();
    }

    /**
     * Indicate if a booking is manageable.
     * Private bookings are manageable only by a member.
     *
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isBookingManageableBy(string $user_id): bool
    {
        return $this->hasOnePermission('booking', [
            'user_id' => $user_id,
        ]);
    }

    /**
     * Indicate if a booking is validable.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return boolean
     */
    public function isBookingValidableBy(\Illuminate\Database\Eloquent\Model $model): bool
    {
        if ($model instanceof Asso) {
            // Check if the association is a parent of the asking asociation. (Example: the BDE-UTC has the rights on the PAE).
            $toMatch = $this;
            while ($toMatch) {
                if ($toMatch->id === $model->id) {
                    return true;
                }

                $toMatch = $toMatch->parent;
            }

            return false;
        } else if ($model instanceof User) {
            return $this->hasOnePermission('booking', [
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
     * Indicate if a comment is writable.
     * Only people who can write comments are able to write a comment.
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
     * Indicate if a comment is editable.
     * Only people who can write comments are able to edit a comment.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentEditableBy(string $user_id): bool
    {
        return $this->isCommentWritableBy($user_id);
    }

    /**
     * Indicate if a comment is deletable.
     * Only people who can write comments are able to delete a comment.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool
    {
        return $this->isCommentEditableBy($user_id);
    }

    /**
     * Retrieve the name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}

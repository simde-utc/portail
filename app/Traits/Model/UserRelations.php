<?php
/**
 * Trait for describing relations for users.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use App\Models\{
    AuthCas, AuthPassword, AuthApp, Session, UserDetail, UserPreference, Notification,
    Semester, Asso, Group, Calendar, Service, Comment, Contact
};

trait UserRelations
{
    /**
     * Relation with the CAS authentification.
     *
     * @return mixed
     */
    public function cas()
    {
        return $this->hasOne(AuthCas::class);
    }

    /**
     * Relation with password authentification.
     *
     * @return mixed
     */
    public function password()
    {
        return $this->hasOne(AuthPassword::class);
    }

    /**
     * Relation with application authentification.
     *
     * @return mixed
     */
    public function app()
    {
        return $this->hasMany(AuthApp::class);
    }

    /**
     * Relation with details.
     *
     * @return mixed
     */
    public function details()
    {
        return $this->hasMany(UserDetail::class);
    }

    /**
     * Relation with preferences.
     *
     * @return mixed
     */
    public function preferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    /**
     * Relation with notifications.
     *
     * @return mixed
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Relation with associations.
     *
     * @return mixed
     */
    public function assos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->withPivot('semester_id', 'role_id', 'validated_by_id');
    }

    /**
     * Relation with current associations.
     *
     * @return mixed
     */
    public function currentAssos()
    {
        return $this->assos()->where('semester_id', Semester::getThisSemester()->id)->whereNotNull('role_id');
    }

    /**
     * Relation with associations of wich the user is member.
     *
     * @return mixed
     */
    public function joinedAssos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->whereNotNull('validated_by_id')->whereNotNull('role_id')
            ->withPivot('semester_id', 'role_id', 'validated_by_id');
    }

    /**
     * Relation with associations of wich the user is memeber at the current semester.
     *
     * @return mixed
     */
    public function currentJoinedAssos()
    {
        return $this->joinedAssos()->where('semester_id', Semester::getThisSemester()->id);
    }

    /**
     * Relation with associations of which the user asked to join.
     *
     * @return mixed
     */
    public function joiningAssos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->whereNull('validated_by_id')->whereNotNull('role_id')
            ->withPivot('semester_id', 'role_id', 'validated_by_id');
    }

    /**
     * Relation with associations of which the user asked to join at this current semester..
     *
     * @return mixed
     */
    public function currentJoiningAssos()
    {
        return $this->joiningAssos()->where('semester_id', Semester::getThisSemester()->id);
    }

    /**
     * Relation with associtaions that the user is following.
     *
     * @return mixed
     */
    public function followedAssos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->whereNull('role_id')
        ->withPivot('semester_id', 'role_id', 'validated_by_id');
    }

    /**
     * Relation with associtaions that the user is following at the current semester.
     *
     * @return mixed
     */
    public function currentFollowedAssos()
    {
        return $this->followedAssos()->where('semester_id', Semester::getThisSemester()->id);
    }

    /**
     * Relation with groups.
     *
     * @return mixed
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_members');
    }

    /**
     * Relation with followed calendars.
     *
     * @return mixed
     */
    public function followedCalendars()
    {
        return $this->belongsToMany(Calendar::class, 'calendars_followers')->withTimestamps();
    }

    /**
     * Relation with followed services.
     *
     * @return mixed
     */
    public function followedServices()
    {
        return $this->belongsToMany(Service::class, 'services_followers')->withTimestamps();
    }

    /**
     * Relation with comments.
     *
     * @return mixed
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Functions to check user connection depending on different authentification types.
     *
     * @param string $username
     * @return User/null
     */
    public function findForPassport(string $username)
    {
        $providers = config('auth.services');

        foreach ($providers as $provider) {
            $model = $provider['model'];

            if (method_exists($model, 'getUserByIdentifiant')) {
                $user = (new $model)->getUserByIdentifiant($username);
            }

            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Functions to check user connection depending on different authentification types.
     *
     * @param string $password
     * @return boolean
     */
    public function validateForPassportPasswordGrant(string $password)
    {
        $providers = config('auth.services');

        foreach ($providers as $name => $provider) {
            $auth = $this->$name;

            if ($auth) {
                if (method_exists($auth, 'isPasswordCorrect')) {
                    if ($auth->isPasswordCorrect($password)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Indicates if a role is deletable (if it belongs to a unique user).
     * But we allow its deletion if it's assigned to a unique group. 

     * @param  mixed  $role
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleForIdDeletable($role, string $user_id)
    {
        return true;
    }

    /**
     * Returns if a user can access the role.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleAccessibleBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->id === $user_id;
        } else {
            return true;
        }
    }

    /**
     * Indicates if a role is manageable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleManageableBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->id === $user_id;
        } else {
            return $this->hasOnePermission('role');
        }
    }

    /**
     * Idicates if the user can access a permission.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionAccessibleBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->id === $user_id;
        } else {
            return true;
        }
    }

    /**
     * Indicates if a permission is manageable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isPermissionManageableBy(string $user_id): bool
    {
        if ($this->id) {
            return $this->id === $user_id;
        } else {
            return $this->hasOnePermission('permission');
        }
    }

    /**
     * Relation with contacts.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'owned_by');
    }

    /**
     * Indicates if a contact is accessible. 
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactAccessibleBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Indicates if a contact is manageable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactManageableBy(string $user_id): bool
    {
        return $this->id === $user_id;
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
     * Indicates if a calendar is manageable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool
    {
        return $this->id === $user_id;
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
     * Indicates if an event is manageable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Indicates if a comment is writable.
     * Of course we cannot write as someone else. 
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentWritableBy(string $user_id): bool
    {
        return $user_id === $this->id;
    }

    /**
     * Indicates if a comment is editable.
     * Of course we cannot write as someone else. 
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentEditableBy(string $user_id): bool
    {
        return $this->isCommentWritableBy($user_id);
    }

    /**
     * Indicates if a comment is deletable.
     * Of course we cannot delete as someone else. 
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool
    {
        return $this->isCommentEditableBy($user_id);
    }
}

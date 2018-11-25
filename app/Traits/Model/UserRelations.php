<?php
/**
 * Trait servant de descriptif de relation pour les utilisateurs
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
     * Relation avec l'authentification CAS.
     *
     * @return mixed
     */
    public function cas()
    {
        return $this->hasOne(AuthCas::class);
    }

    /**
     * Relation avec l'authentification par mot de passe.
     *
     * @return mixed
     */
    public function password()
    {
        return $this->hasOne(AuthPassword::class);
    }

    /**
     * Relation avec les authentifications applications.
     *
     * @return mixed
     */
    public function app()
    {
        return $this->hasMany(AuthApp::class);
    }

    /**
     * Relation avec les sessions.
     *
     * @return mixed
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Relation avec les détails.
     *
     * @return mixed
     */
    public function details()
    {
        return $this->hasMany(UserDetail::class);
    }

    /**
     * Relation avec les préférences.
     *
     * @return mixed
     */
    public function preferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    /**
     * Relation avec les notifications.
     *
     * @return mixed
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Relation avec les associations.
     *
     * @return mixed
     */
    public function assos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->withPivot('semester_id', 'role_id', 'validated_by');
    }

    /**
     * Relation avec les actuelles associations.
     *
     * @return mixed
     */
    public function currentAssos()
    {
        return $this->assos()->where('semester_id', Semester::getThisSemester()->id)->whereNotNull('role_id');
    }

    /**
     * Relation avec les associations dont l'utilisateur est membre.
     *
     * @return mixed
     */
    public function joinedAssos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->whereNotNull('validated_by')->whereNotNull('role_id')
        ->withPivot('semester_id', 'role_id', 'validated_by');
    }

    /**
     * Relation avec les associations dont l'utilisateur est membre au semestre actuel.
     *
     * @return mixed
     */
    public function currentJoinedAssos()
    {
        return $this->joinedAssos()->where('semester_id', Semester::getThisSemester()->id);
    }

    /**
     * Relation avec les associations où l'utilisateur a demandé de rejoindre.
     *
     * @return mixed
     */
    public function joiningAssos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->whereNull('validated_by')->whereNotNull('role_id')
        ->withPivot('semester_id', 'role_id', 'validated_by');
    }

    /**
     * Relation avec les associations où l'utilisateur a demandé de rejoindre au semestre actuel.
     *
     * @return mixed
     */
    public function currentJoiningAssos()
    {
        return $this->joiningAssos()->where('semester_id', Semester::getThisSemester()->id);
    }

    /**
     * Relation avec les associations que l'utilisateur suit.
     *
     * @return mixed
     */
    public function followedAssos()
    {
        return $this->belongsToMany(Asso::class, 'assos_members')->whereNull('role_id')
        ->withPivot('semester_id', 'role_id', 'validated_by');
    }

    /**
     * Relation avec les associations que l'utilisateur suit au semestre actuel.
     *
     * @return mixed
     */
    public function currentFollowedAssos()
    {
        return $this->followedAssos()->where('semester_id', Semester::getThisSemester()->id);
    }

    /**
     * Relation avec les groupes.
     *
     * @return mixed
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_members');
    }

    /**
     * Relation avec les groupes encore actifs.
     *
     * @return mixed
     */
    public function currentGroups()
    {
        return $this->groups()->where('is_active', 1);
    }

    /**
     * Relation avec les groupes créés par l'utilisateur.
     *
     * @return mixed
     */
    public function ownGroups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Relation avec les calendriers suivits.
     *
     * @return mixed
     */
    public function followedCalendars()
    {
        return $this->belongsToMany(Calendar::class, 'calendars_followers')->withTimestamps();
    }

    /**
     * Relation avec les services suivis.
     *
     * @return mixed
     */
    public function followedServices()
    {
        return $this->belongsToMany(Service::class, 'services_followers')->withTimestamps();
    }

    /**
     * Relation avec les commentaires.
     *
     * @return mixed
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Fonctions permettant de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
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
     * Fonctions permettant de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
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
     * Indique si un rôle est supprimable s'il appartient à un utilisateur unique.
     * Mais on permet sa suppression s'il est assigné à un seul groupe.

     * @param  mixed  $role
     * @param  string $user_id
     * @return boolean
     */
    public function isRoleForIdDeletable($role, string $user_id)
    {
        return true;
    }

    /**
     * Indique si un rôle est accessible.
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
     * Indique si un rôle est gérable.
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
     * Indique si une permission est accessible.
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
     * Indique si une permission est gérable.
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
     * Relation avec les contacts.
     *
     * @return mixed
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'owned_by');
    }

    /**
     * Indique si un contact est accessible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactAccessibleBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Indique si un contact est gérable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isContactManageableBy(string $user_id): bool
    {
        return $this->id === $user_id;
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
     * Indique si un calendrier est accessible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarAccessibleBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Indique si un calendrier est gérable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCalendarManageableBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Relation avec les événements.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->morphMany(Event::class, 'owned_by');
    }

    /**
     * Indique si un événement est accessible.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventAccessibleBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Indique si un événement est gérable.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isEventManageableBy(string $user_id): bool
    {
        return $this->id === $user_id;
    }

    /**
     * Indique si un commentaire est écrivable.
     * On ne peut bien sûr pas écrire au nom de quelqu'un d'autre.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentWritableBy(string $user_id): bool
    {
        return $user_id === $this->id;
    }

    /**
     * Indique si un commentaire est éditable.
     * On ne peut bien sûr pas écrire au nom de quelqu'un d'autre.
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
     * On ne peut bien sûr pas supprimer au nom de quelqu'un d'autre.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentDeletableBy(string $user_id): bool
    {
        return $this->isCommentEditableBy($user_id);
    }
}

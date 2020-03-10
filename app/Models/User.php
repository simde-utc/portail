<?php
/**
 * Model corresponding to users.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Cog\Contracts\Ownership\CanBeOwner;
use App\Interfaces\Model\{
    CanBeNotifiable, CanHaveCalendars, CanHaveContacts, CanHaveEvents, CanHaveRoles, CanHavePermissions, CanComment,
    CanHaveArticles
};
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Traits\Model\{
    HasRoles, HasHiddenData, HasUuid, UserRelations, IsLogged
};
use NastuzziSamy\Laravel\Traits\HasSelection;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Semester, UserPreference, UserDetail
};
use App\Http\Requests\ContactRequest;
use App\Exceptions\PortailException;
use App\Notifications\Notification;
use App\Notifications\User\{
    UserCreation, UserDesactivation, UserModification, UserDeletion
};

class User extends Authenticatable implements CanBeNotifiable, CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents,
	CanHaveRoles, CanHavePermissions, CanComment, CanHaveArticles
{
    use HasHiddenData, HasSelection, HasApiTokens, Notifiable, HasRoles, HasUuid, IsLogged, UserRelations {
        UserRelations::notifications insteadof Notifiable;
        UserRelations::isRoleForIdDeletable insteadof HasRoles;
        HasHiddenData::hideData as protected hideDataFromTrait;
    }

    /**
     * Launched at the model creation.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // Add to preferences.
            $model->preferences()->create([
                'key' => 'NOTIFICATION_CHANNELS',
                'value' => [
                    'mail', 'database', 'push'
                ],
            ]);

            $model->preferences()->create([
                'key' => 'CONTACT_EMAIL',
                'value' => $model->isActive() ? $model->email : null,
            ]);

            $model->preferences()->create([
                'key' => 'NOTIFICATION_EMAIL_AVOID',
                'value' => [],
            ]);

            $model->preferences()->create([
                'key' => 'NOTIFICATION_PUSH_AVOID',
                'value' => [],
            ]);

            if ($model->isActive()) {
                $model->notify(new UserCreation());
            }
        });

        static::updated(function ($model) {
            // Preferences update.
            if ($model->preferences()->valueOf('CONTACT_EMAIL') === $model->getOriginal('email')) {
                $model->preferences()->key('CONTACT_EMAIL')->update([
                    'value' => $model->is_active ? $model->email : null,
                ]);
            }

            if (((bool) $model->getOriginal('is_active')) !== ((bool) $model->getAttribute('is_active'))) {
                $model->notify($model->isActive() ? new UserCreation() : new UserDesactivation());
            }

            $model->notifyOnEdition();
        });

        static::deleting(function ($model) {
            $model->notify(new UserDeletion());
        });
    }

    public $incrementing = false;

    protected $fillable = [
        'firstname', 'lastname', 'email', 'image', 'is_active', 'last_login_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'name',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $must = [
        'image'
    ];

    protected $possibleTypes = [
        'admin' => 'administrateur',
        'member' => 'membre d\'une association',
        'contributorBde' => 'cotisant BDE',
        'casConfirmed' => 'membre UTC/ESCOM',
        'cas' => 'avec connexion CAS',
        'password' => 'avec connexion email/mot de passe',
        'active' => 'compte actif',
    ];

    protected $selection = [
        'order' => 'oldest',
        'paginate' => null,
        'filter' => [],
    ];

    /**
     * Create the "me" attributes on the fly.
     *
     * @return mixed
     */
    public function getMeAttribute()
    {
        return \Auth::id() === $this->id;
    }

    /**
     * Create the "name" attribute on the fly. (First and last name concatenation).
     *
     * @return string
     */
    public function getNameAttribute()
    {
        if ($this->isActive()) {
            return $this->firstname.' '.strtoupper($this->lastname);
        } else {
            return 'Compte invité';
        }
    }

    /**
     * Retrieve the user's password.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password()->password;
    }

    /**
     * Find a user with his email adress.
     *
     * @param  string $email
     * @return User|null
     */
    public static function findByEmail(string $email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Retrieve a user list depending on precise information.
     *
     * @param  mixed $users
     * @return mixed
     */
    public static function getUsers($users)
    {
        if (is_array($users)) {
            return static::whereIn('id', $users)->orWhereIn('email', $users)->get();
        } else if ($users instanceof \Illuminate\Database\Eloquent\Model) {
            return collect($users);
        } else {
            return $users;
        }
    }

    /**
     * Return the notification channels list.
     *
     * @param  string $notificationType
     * @return array
     */
    public function notificationChannels(string $notificationType): array
    {
        $channels = $this->preferences()->valueOf('NOTIFICATION_CHANNELS');

        if (in_array($notificationType, $this->preferences()->valueOf('NOTIFICATION_EMAIL_AVOID'))
        	&& ($key = array_search('mail', $channels)) !== false) {
            unset($channels[$key]);
        }

        if (in_array($notificationType, $this->preferences()->valueOf('NOTIFICATION_PUSH_AVOID'))
        	&& ($key = array_search('push', $channels)) !== false) {
            unset($channels[$key]);
        }

        if (!$this->isActive() && ($key = array_search('mail', $channels)) !== false) {
            unset($channels[$key]);
        }

        if (!$this->isApp() && ($key = array_search('push', $channels)) !== false) {
            unset($channels[$key]);
        }

        return $channels;
    }

    /**
     * Return the notification email adress.
     *
     * @param  mixed $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        if ($this->preferences()->keyExistsInDB('CONTACT_EMAIL')) {
            return $this->preferences()->valueOf('CONTACT_EMAIL');
        } else {
            return $this->email;
        }
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
     * Notify the user in case of update.
     *
     * @return boolean
     */
    public function notifyOnEdition()
    {
        $edited = [];

        if ($this->getOriginal('email') !== $this->getAttribute('email')) {
            $edited['Adresse email'] = $this->email;
        }

        if ($this->getOriginal('lastname') !== $this->getAttribute('lastname')) {
            $edited['Nom'] = $this->lastname;
        }

        if ($this->getOriginal('firstname') !== $this->getAttribute('firstname')) {
            $edited['Prénom'] = $this->firstname;
        }

        if ($this->getOriginal('image') !== $this->getAttribute('image')) {
            $edited['Photo de profil'] = 'changée !';
        }

        if (count($edited) > 0) {
            $this->notify(new UserModification($edited));

            return true;
        }

        return false;
    }

    /**
     * Return all possible user types.
     *
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->possibleTypes);
    }

    /**
     * Return all possible user types with their description.
     *
     * @return array
     */
    public function getTypeDescriptions()
    {
        return $this->possibleTypes;
    }

    /**
     * Return if the user is of the given type.
     *
     * @param string $type
     * @return boolean
     */
    public function isType(string $type)
    {
        $method = 'is'.ucfirst($type);

        return method_exists($this, $method) && $this->$method();
    }

    /**
     * Return the major type of the current user (admin, cas, contributorBDE, and so on.).
     *
     * @return string|null
     */
    public function type()
    {
        foreach ($this->getTypes() as $type) {
            $method = 'is'.ucfirst($type);

            if (method_exists($this, $method) && $this->$method()) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Indicate if the user is a public user.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return true;
    }

    /**
     * Indicate if the user a active (implied: he has been connected at least once).
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->is_active === null || ((bool) $this->is_active) === true;
    }

    /**
     * Indicate if the user is or has been a CAS-UTC member.
     *
     * @return boolean
     */
    public function isCas()
    {
        $cas = $this->cas()->first();

        return $cas && $cas->is_active;
    }

    /**
     * Indicate if the user is and still is a CAS member.
     *
     * @return boolean
     */
    public function isCasConfirmed()
    {
        $cas = $this->cas()->first();

        return $cas && $cas->is_confirmed;
    }

    /**
     * Indicate if the user can login trough password authentification.
     *
     * @return boolean
     */
    public function isPassword()
    {
        return $this->password()->exists();
    }

    /**
     * Indicate if the user is connected trough an application.
     *
     * @return boolean
     */
    public function isApp()
    {
        return $this->app()->exists();
    }

    /**
     * Indicate if the user is contributor to the BDE-UTC.
     *
     * @return boolean|null
     */
    public function isContributorBde()
    {
        try {
            return $this->details()->valueOf('isContributorBde');
        } catch (PortailException $e) {
            if (config('app.debug', false) && config('app.admin.email') === $this->email) {
                return true;
            }

            return null;
        }
    }

    /**
     * Indicate if the user is member of an association.
     *
     * @return boolean
     */
    public function isMember()
    {
        return $this->currentJoinedAssos()->count() > 0;
    }

    /**
     * Indicats if the user is administrator.
     * Yeah bro <3
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->hasOneRole(config('portail.roles.admin.users'));
    }

    /**
     * Return the lang of the user.
     *
     * @return string
     */
    public function getLang()
    {
        try {
            return $this->preferences()->valueOf('lang');
        } catch (PortailException $e) {
            return 'fr';
        }
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
     * Indicate if an article is manageable.
     * A user article is manageable by it's owner
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isArticleManageableBy(string $user_id): bool
    {
        return $this->id == $user_id;
    }
}

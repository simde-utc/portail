<?php
/**
 * Modèle correspondant aux utilisateurs.
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
use App\Interfaces\Model\CanBeNotifiable;
use Cog\Contracts\Ownership\CanBeOwner;
use App\Interfaces\Model\CanHaveCalendars;
use App\Interfaces\Model\CanHaveContacts;
use App\Interfaces\Model\CanHaveEvents;
use App\Interfaces\Model\CanHaveRoles;
use App\Interfaces\Model\CanHavePermissions;
use App\Interfaces\Model\CanComment;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Traits\Model\HasRoles;
use App\Traits\Model\HasHiddenData;
use App\Traits\Model\HasUuid;
use App\Traits\Model\UserRelations;
use NastuzziSamy\Laravel\Traits\HasSelection;
use Illuminate\Support\Facades\Auth;
use App\Models\Semester;
use App\Models\UserPreference;
use App\Models\UserDetail;
use App\Http\Requests\ContactRequest;
use App\Exceptions\PortailException;
use App\Notifications\User\UserCreation;
use App\Notifications\User\UserDesactivation;
use App\Notifications\User\UserModification;
use App\Notifications\User\UserDeletion;

class User extends Authenticatable implements CanBeNotifiable, CanBeOwner, CanHaveContacts, CanHaveCalendars, CanHaveEvents,
	CanHaveRoles, CanHavePermissions, CanComment
{
    use HasHiddenData, HasSelection, HasApiTokens, Notifiable, HasRoles, HasUuid, UserRelations {
        UserRelations::notifications insteadof Notifiable;
        UserRelations::isRoleForIdDeletable insteadof HasRoles;
        HasHiddenData::hideData as protected hideDataFromTrait;
    }

    /**
     * Lancé à la création du modèle.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // Ajout dans les préférences.
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
            // Modfication des préférences.
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

    protected $types = [
        'admin' => 'administrateur',
		'contributorBde' => 'contributeur BDE',
		'casConfirmed' => 'membre UTC ou ESCOM',
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
     * Crée l'attribut me à la volée.
     *
     * @return mixed
     */
    public function getMeAttribute()
    {
        return \Auth::id() === $this->id;
    }

    /**
     * Créer l'attribut name à la volée (concaténation du prénom et du nom).
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
     * Récupère le mot de passe de l'utilisateur.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password()->password;
    }

    /**
     * Retrouve un utilisateur par son adresse email.
     *
     * @param  string $email
     * @return User|null
     */
    public static function findByEmail(string $email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Récupère une liste des utilisateurs en fonction d'informations précises.
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
     * Donne la liste des canaux de notifications.
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
     * Donne l'adresse email de notification.
     *
     * @param  mixed $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->preferences()->keyExistsInDB('CONTACT_EMAIL') ? $this->preferences()->valueOf('CONTACT_EMAIL') : null;
    }

    /**
     * Notifie l'utilisateur en cas de modification.
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
     * Retourne les types d'utilisateurs possible.
     *
     * @return array
     */
    public function getTypes()
    {
        return array_keys($this->types);
    }

    /**
     * Retourne les types d'utilisateurs possible avec leur description.
     *
     * @return array
     */
    public function getTypeDescriptions()
    {
        return $this->types;
    }

    /**
     * Retourne les types d'utilisateurs possible.
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
     * Donne le type majeur de l'utilisateur (admin, cas, cotisant, etc.).
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
     * Indique si l'utilsateur est du public.
     *
     * @return boolean
     */
    public function isPublic()
    {
        return true;
    }

    /**
     * Indique si l'utilsateur est actif (sous-entendu, qu'il s'est bien déjà connecté une fois).
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->is_active === null || ((bool) $this->is_active) === true;
    }

    /**
     * Indique si l'utilsateur a été un CAS-UTC.
     *
     * @return boolean
     */
    public function isCas()
    {
        $cas = $this->cas()->first();

        return $cas && $cas->is_active;
    }

    /**
     * Indique si l'utilsateur est du CAS-UTC et l'est toujours.
     *
     * @return boolean
     */
    public function isCasConfirmed()
    {
        $cas = $this->cas()->first();

        return $cas && $cas->is_confirmed;
    }

    /**
     * Indique si l'utilsateur peut se connecter via mot de passe.
     *
     * @return boolean
     */
    public function isPassword()
    {
        return $this->password()->exists();
    }

    /**
     * Indique si l'utilsateur est connecté via une application.
     *
     * @return boolean
     */
    public function isApp()
    {
        return $this->app()->exists();
    }

    /**
     * Indique si l'utilsateur est cotisant BDE-UTC.
     *
     * @return boolean|null
     */
    public function isContributorBde()
    {
        try {
            return $this->details()->valueOf('isContributorBde');
        } catch (PortailException $e) {
            return null;
        }
    }

    /**
     * Indique si l'utilsateur est un administrateur.
     * Yeah l'ami <3
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->hasOneRole(config('portail.roles.admin.users'));
    }
}

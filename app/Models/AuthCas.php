<?php
/**
 * Modèle correspondant aux authentifications CAS-UTC.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Traits\Model\HasHiddenData;
use App\Notifications\Auth\RememberToLinkCAS;
use NastuzziSamy\Laravel\Traits\HasSelection;

class AuthCas extends Auth
{
    use HasHiddenData, HasSelection;

    public $incrementing = false;

    protected $fillable = [
        'user_id', 'login', 'email', 'last_login_at', 'is_active', 'is_confirmed',
    ];

    // Si on se connecte via passsword, on désactive tout ce qui est relié au CAS car on suppose qu'il n'est plus étudiant.
    protected $casts = [
        'is_active' => 'boolean',
        'is_confirmed' => 'boolean',
    ];

    protected $must = [
        'user_id', 'login', 'email', 'is_active', 'is_confirmed',
    ];

    /**
     * Lancer à la création du modèle.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // On crée une notif de rappel de linkage.
            $user = $model->user;

            if (!$user->image) {
                $user->image = config('portail.cas.image').$model->login;
                $user->save();
            }

            if (!$user->isPassword()) {
                $user->notify(new RememberToLinkCAS());
            }
        });
    }

    /**
     * Permet de retrouver par email.
     * TODO: transformer en scope.
     *
     * @param  string $email
     * @return AuthCas
     */
    public static function findByEmail(string $email)
    {
        return (new static)->where('email', $email)->first();
    }

    /**
     * Relation avec l'utlisateur.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Permet de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
     *
     * @param string $login
     * @return mixed
     */
    public function getUserByIdentifiant(string $login)
    {
        $auth = $this->where('login', $login)->first();

        if ($auth) {
            return $auth->user;
        } else {
            $ginger = \Ginger::user($login);

            if ($ginger->exists()) {
                $user = new User;
                $user->email = $ginger->getEmail();
                $user->firstname = $ginger->getFirstname();
                $user->lastname = $ginger->getLastname();
                $user->is_active = true;

                $cas = new self;
                $cas->email = $ginger->getEmail();
                $cas->login = $login;

                $user->cas = $cas;
                $cas->user = $user;

                return $user;
            }
        }

        return null;
    }

    /**
     * Permet de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
     *
     * @param string $password
     * @return boolean
     */
    public function isPasswordCorrect(string $password)
    {
        $curl = \Curl::to(config('portail.cas.url').'v1/tickets')
        ->withData([
            'username' => $this->login,
            'password' => $password,
        ])
        ->returnResponseObject();

        if (strpos($_SERVER['HTTP_HOST'], 'utc.fr')) {
            $curl = $curl->withProxy('proxyweb.utc.fr', 3128);
        }

        $connected = $curl->post()->status === 201;

        // On doit donc créer le compte.
        if ($connected && $this->user_id === null) {
            $user = User::firstOrCreate([
                'email' => $this->user->email,
            ], [
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
            ]);

            self::firstOrCreate([
                'email' => $this->email,
            ], [
                'user_id' => $user->id,
                'login' => $this->login,
            ]);
        }

        return $connected;
    }
}

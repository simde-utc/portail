<?php
/**
 * Model corresponding to CAS-UTC authentifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Notifications\Auth\RememberToLinkCAS;

class AuthCas extends Auth
{
    protected $fillable = [
        'user_id', 'login', 'email', 'last_login_at', 'is_active', 'is_confirmed',
    ];

    // If there if a password connection, all that is CAS-related is disabled because we supose he no longer is a student. 
    protected $casts = [
        'is_active' => 'boolean',
        'is_confirmed' => 'boolean',
    ];

    protected $must = [
        'user_id', 'login', 'email', 'is_active', 'is_confirmed',
    ];

    /**
     * Called at the model creation.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            // Linkage reminder notification creation.
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
     * Returns a user from its email adress.
     * TODO: transform into scope.
     *
     * @param  string $email
     * @return AuthCas|null
     */
    public static function findByEmail(string $email)
    {
        return (new static)->where('email', $email)->first();
    }

    /**
     * Retrieves a user from its login.
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
     * Checks if the password is correct.
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

        if (strpos(request()->getHttpHost(), 'utc.fr')) {
            $curl = $curl->withProxy('proxyweb.utc.fr', '3128');
        }

        $connected = $curl->post()->status === 201;

        // We have to create the account.
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

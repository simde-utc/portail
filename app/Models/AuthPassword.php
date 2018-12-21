<?php
/**
 * Modèle correspondant aux authentifications mots de passe.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;

class AuthPassword extends Auth
{
    protected $fillable = [
        'user_id', 'password', 'last_login_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $must = [
        'user_id',
    ];

    /**
     * Permet de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
     *
     * @param string $email
     * @return mixed
     */
    public function getUserByIdentifiant(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Permet de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
     *
     * @param string $password
     * @return boolean
     */
    public function isPasswordCorrect(string $password)
    {
        return Hash::check($password, $this->password);
    }
}

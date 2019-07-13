<?php
/**
 * Model corresponding to password authentifications.
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
     * Retrieves a user from its email adress.
     *
     * @param string $email
     * @return mixed
     */
    public function getUserByIdentifiant(string $email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Checks if a password is correct or not.
     *
     * @param string $password
     * @return boolean
     */
    public function isPasswordCorrect(string $password)
    {
        return Hash::check($password, $this->password);
    }
}

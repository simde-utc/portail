<?php
/**
 * Abstract model for authentifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Traits\Model\{
    HasHiddenData, IsLogged
};
use NastuzziSamy\Laravel\Traits\HasSelection;

abstract class Auth extends Model
{
    use HasHiddenData, HasSelection, IsLogged;

    public $incrementing = false;

    /**
     * Relation with the user.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve a user by a unique data.
     *
     * @param string $username
     * @return mixed
     */
    abstract public function getUserByIdentifiant(string $username);

    /**
     * Check if the password is correct.
     *
     * @param string $password
     * @return boolean
     */
    abstract public function isPasswordCorrect(string $password);
}

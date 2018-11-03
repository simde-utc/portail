<?php
/**
 * Modèle abstrait correspondant aux authentifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

abstract class Auth extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'user_id';

    /**
     * Relation avec l'utilisateur.
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
     * @param string $username
     * @return mixed
     */
    abstract public function getUserByIdentifiant(string $username);

    /**
     * Permet de vérifier la connexion d'un utilisateur en fonction des différents types d'authentification.
     *
     * @param string $password
     * @return boolean
     */
    abstract public function isPasswordCorrect(string $password);
}

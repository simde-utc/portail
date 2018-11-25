<?php
/**
 * Modèle correspondant aux authentifications applications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use App\Traits\Model\HasHiddenData;
use NastuzziSamy\Laravel\Traits\HasSelection;

class AuthApp extends Auth
{
    use HasHiddenData, HasSelection;

    public $incrementing = false;

    protected $fillable = [
        'user_id', 'app_id', 'password', 'key',
    ];

    protected $hidden = [
        'password',
    ];

    protected $must = [
        'user_id', 'app_id',
    ];

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
     * @param string $app_id
     * @return mixed
     */
    public function getUserByIdentifiant(string $app_id)
    {
        $app = static::where('app_id', $app_id)->first();

        if ($app) {
            $user = $app->user;
            $user->app = $app;

            return $user;
        } else {
            return null;
        }
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

<?php

namespace App\Services\Auth;
use Illuminate\Support\Facades\Auth as Authentification;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreferences;

abstract class Auth
{
    protected static $namespace = 'App\\Models\\';

    protected static function findUser($model, $key, $value) {
        return (self::$namespace.$model)::where($key, $value)->first();
    }

    protected static function create($model, $email, $lastname, $firstname, $infos = []) {
        // Création de l'utilisateur avec les informations minimales
        $user = self::createUser($email, $lastname, $firstname);

        // On crée le système d'authentification
        $userAuth = self::createAuth($model, $user->id, $infos);

        // Si tout est bon, on le connecte
        if ($user !== null && $userAuth !== null)
            self::connect($user);
    }

    protected static function update($model, $id, $lastname, $firstname, $infos = []) {
        // Actualisation des informations
        $user = self::updateUser($id, $lastname, $firstname);

        // On actualise le système d'authentification
        $userAuth = self::updateAuth($model, $id, $infos);

        // Si tout est bon, on le connecte
        if ($user !== null && $userAuth !== null)
            self::connect($user);
    }

    protected static function createUser($email, $lastname, $firstname) {
        $user = User::create([
          'email' => $email,
          'lastname' => $lastname,
          'firstname' => $firstname,
          'last_login_at' => new \DateTime()
        ]);

        // Ajout dans les préférences
        $userPreferences = UserPreferences::create([
          'user_id' => $user->id,
          'email' => $email,
        ]);

        return $user;
    }

    protected static function updateUser($id, $lastname, $firstname) {
        $user = User::find($id);
        $user->lastname = $lastname;
        $user->firstname = $firstname;
        $user->save();

        $user->timestamps = false;
        $user->last_login_at = new \DateTime();
        $user->save();

        return $user;
    }

    protected static function createAuth($model, $id, $infos = []) {
        return (self::$namespace.$model)::create(array_merge($infos, [
          'user_id' => $id,
          'last_login_at' => new \DateTime(),
        ]));
    }

    protected static function updateAuth($model, $id, $infos = []) {
        $userAuth = (self::$namespace.$model)::find($id);

        foreach ($infos as $key => $value)
          $userAuth->$key = $value;

        $userAuth->save();

        $userAuth->timestamps = false;
        $userAuth->last_login_at = new \DateTime();
        $userAuth->save();

        return $userAuth;
    }

    protected static function connect($user) {
        Authentification::login($user);
    }
}

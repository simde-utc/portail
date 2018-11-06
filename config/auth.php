<?php

return [
    'services' => [
        'cas' => [
            'name' => 'CAS-UTC',
            'description' => 'Connexion (CAS) réservée aux membres UTC',
            'class' => App\Services\Auth\Cas::class,
            'model' => App\Models\AuthCas::class,
            'loggable' => true,
            'registrable' => false,
        ],

        'password' => [
            'name' => 'Mot de passe',
            'description' => 'Connexion par adresse email et mot de passe (possibilité de créer un compte)',
            'class' => App\Services\Auth\Password::class,
            'model' => App\Models\AuthPassword::class,
            'loggable' => true,
            'registrable' => true,
        ],

        'app' => [
            'name' => 'Application',
            'description' => 'Connexion pour l\'application',
            'class' => App\Services\Auth\App::class,
            'model' => App\Models\AuthApp::class,
            'loggable' => false,
            'registrable' => false,
        ],
    ],

    /*
        |--------------------------------------------------------------------------
        | Authentication Defaults
        |--------------------------------------------------------------------------
        |
        | This option controls the default authentication "guard" and password
        | reset options for your application. You may change these defaults
        | as required, but they're a perfect start for most applications.
        |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
        |--------------------------------------------------------------------------
        | Authentication Guards
        |--------------------------------------------------------------------------
        |
        | Next, you may define every authentication guard for your application.
        | Of course, a great default configuration has been defined for you
        | here which uses session storage and the Eloquent user provider.
        |
        | All authentication drivers have a user provider. This defines how the
        | users are actually retrieved out of your database or other storage
        | mechanisms used by this application to persist your user's data.
        |
        | Supported: "session", "token"
        |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],

    /*
        |--------------------------------------------------------------------------
        | User Providers
        |--------------------------------------------------------------------------
        |
        | All authentication drivers have a user provider. This defines how the
        | users are actually retrieved out of your database or other storage
        | mechanisms used by this application to persist your user's data.
        |
        | If you have multiple user tables or models you may configure multiple
        | sources which represent each model / table. These sources may then
        | be assigned to any extra authentication guards you have defined.
        |
        | Supported: "database", "eloquent"
        |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
            'table' => 'users'
        ],
    ],

    /*
        |--------------------------------------------------------------------------
        | Resetting Passwords
        |--------------------------------------------------------------------------
        |
        | You may specify multiple password reset configurations if you have more
        | than one user table or model in the application and you want to have
        | separate password reset settings based on the specific user types.
        |
        | The expire time is the number of minutes that the reset token should be
        | considered valid. This security feature keeps tokens short-lived so
        | they have less time to be guessed. You may change this as needed.
        |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'auth_passwords_resets',
            'expire' => 60,
        ],
    ],

];

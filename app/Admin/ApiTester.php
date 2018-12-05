<?php

namespace App\Admin;

use Encore\Admin\ApiTester\ApiTester as BaseApiTester;
use App\Models\User;
use Laravel\Passport\Passport;

class ApiTester extends BaseApiTester
{
    /**
     * Retrouve l'utilisateur pour l'api-tester.
     *
     * @param  $user_id
     * @return User
     */
    protected function getUser($auth, $user_id)
    {
        if (config('app.debug')) {
            if (\Uuid::validate($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = User::where('email', $user_id)->first();
            }
        }
        else {
            $user = \Auth::guard('web')->user();
        }

        if (is_null($user)) {
            abort(400, 'Mauvais utilisateur');
        }

        $tokenClass = Passport::tokenModel();
        $token = new $tokenClass();
        $token->scopes = \Scopes::getDevScopes();
        $user->withAccessToken($token);

        return $user;
    }
}

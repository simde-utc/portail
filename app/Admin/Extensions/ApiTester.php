<?php
/**
 * Provide an API tester.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Extensions;

use Encore\Admin\ApiTester\ApiTester as BaseApiTester;
use App\Models\User;
use Laravel\Passport\Passport;

class ApiTester extends BaseApiTester
{
    /**
     * Get the user for the API-tester.
     *
     * @param  mixed $auth
     * @param  mixed $user_id
     * @return User
     */
    protected function getUser($auth, $user_id)
    {
        if (is_null($user_id)) {
            $user = User::find(\Auth::guard('admin')->id());
        } else {
            if (\Uuid::validate($user_id)) {
                $user = User::find($user_id);
            } else {
                $user = User::where('email', $user_id)->first();
            }

            if (!config('app.debug')) {
                if (\Auth::guard('admin')->id() !== $user->id) {
                    abort(400, 'Il n\'est pas possible de spécifier un utilisateur autre.');
                }
            }
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

<?php

namespace App\Http\Controllers\Admin;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use App\Models\Admin;

class AuthController extends BaseAuthController
{
    public function getLogin() {
        if (\Auth::guard('web')->check()) {
            if (($user = \Auth::guard('web')->user())->getUserPermissions()->count()) {
                $this->guard()->login(Admin::find($user->getKey()));

                return redirect(config('admin.route.prefix'));
            }
            else {
                abort(403, 'Accès refusé');
            }
        }
        else {
            return redirect('/login');
        }
    }
}

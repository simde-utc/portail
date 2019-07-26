<?php
/**
 * Authenticate admins.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AuthController as BaseAuthController;
use App\Admin\Models\Admin;
use Illuminate\Http\Request;

class AuthController extends BaseAuthController
{
    /**
     * Connection check.
     *
     * @return mixed
     */
    public function getLogin()
    {
        if (\Auth::guard('web')->check()) {
            if (($user = \Auth::guard('web')->user())->getUserPermissions()->count()) {
                $this->guard()->login(Admin::find($user->getKey()));

                return redirect(redirect()->intended()->getTargetUrl() ?: config('admin.route.prefix'));
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/login');
        }
    }

    /**
     * Logout.
     *
     * @param Request $request
     * @return mixed
     */
    public function getLogout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}

<?php
/**
 * Base authentification service.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserPreference;
use App\Models\Session;

abstract class BaseAuth
{
    /**
     * Attributes to define.
     */
    protected $name;
    protected $config;

    /**
     * Return a link to the login form.
     *
     * @param Request $request
     * @return mixed
     */
    public function showLoginForm(Request $request)
    {
        return view(
            'auth.'.$this->name.'.login',
            ['provider' => $this->name, 'redirect' => $request->query('redirect', url()->previous())]
        );
    }

    /**
     * Return a link to to subcribing form.
     *
     * @param Request $request
     * @return mixed
     */
    public function showRegisterForm(Request $request)
    {
        if ($this->config['registrable']) {
            return view(
	            'auth.'.$this->name.'.register',
	            ['provider' => $this->name, 'redirect' => $request->query('redirect', url()->previous())]
            );
        } else {
            return redirect()->route(
	            'register.show',
	            ['redirect' => $request->query('redirect', url()->previous())]
            )->cookie('auth_provider', '', config('portail.cookie_lifetime'));
        }
    }

    /**
     * Connection method.
     *
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        return null;
    }

    /**
     * Subscribing method.
     *
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        return null;
    }

    /**
     * Logout method.
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        return null;
    }

    /**
     * Find a user trough the model wich correspond at the authentification mode.
     *
     * @param string $key
     * @param string $value
     * @return mixed
     */
    protected function findUser(string $key, string $value)
    {
        return resolve($this->config['model'])::where($key, $value)->first();
    }

    /**
     * Create a user and his connection mode auth_{provider}.
     *
     * @param Request $request
     * @param array   $userInfo
     * @param array   $authInfo
     * @return mixed
     */
    protected function create(Request $request, array $userInfo, array $authInfo)
    {
        // User creation with minimal information.
        try {
            $user = $this->createUser($userInfo);
        } catch (\Exception $e) {
            return $this->error($request, null, null, 'Cette adresse mail est déjà utilisée');
        }

        // Auth system creation.
        $userAuth = $this->createAuth($user->id, $authInfo);

        return $this->connect($request, $user, $userAuth);
    }

    /**
     * Update user information and his connection mode auth_{provider}.
     *
     * @param Request $request
     * @param string  $user_id
     * @param array   $userInfo
     * @param array   $authInfo
     * @return mixed
     */
    protected function update(Request $request, string $user_id, array $userInfo=[], array $authInfo=[])
    {
        // Information update.
        $user = $this->updateUser($user_id, $userInfo);

        // Auth system actualisation.
        $userAuth = $this->updateAuth($user_id, $authInfo);

        return $this->connect($request, $user, $userAuth);
    }

    /**
     * Create or adjust user information and his connection mode auth_{provider}.
     *
     * @param Request $request
     * @param string  $key
     * @param string  $value
     * @param array   $userInfo
     * @param array   $authInfo
     * @return mixed
     */
    protected function updateOrCreate(Request $request, string $key, string $value, array $userInfo=[], array $authInfo=[])
    {
        // Find a user.
        $userAuth = $this->findUser($key, $value);

        if ($userAuth === null) {
            $user = isset($userInfo['email']) ? User::where('email', $userInfo['email'])->first() : null;

            if ($user === null) {
                try {
                    return $this->create($request, $userInfo, $authInfo);
                    // If known, we create and connect him.
                } catch (\Exception $e) {
                    return $this->error(
                        $request, null, null,
                    	'Cette adresse mail est déjà utilisé mais n\'est pas relié au bon compte'
                    );
                }
            } else {
                $user = $this->updateUser($user->id, $userInfo);
                $userAuth = $this->createAuth($user->id, $authInfo);

                return $this->connect($request, $user, $userAuth);
            }
        } else {
            return $this->update($request, $userAuth->user_id, $userInfo, $authInfo);
            // If known, we update and connect his information.
        }
    }

    /**
     * Create user.
     *
     * @param array $info
     * @return mixed
     */
    protected function createUser(array $info)
    {
        $user = User::create([
            'email' => $info['email'],
            'lastname' => $info['lastname'],
            'firstname' => $info['firstname'],
            'is_active' => true,
        ]);

        return $user;
    }

    /**
     * Update user.
     *
     * @param string $user_id
     * @param array  $info
     * @return mixed
     */
    protected function updateUser(string $user_id, array $info=[])
    {
        $user = User::find($user_id);

        if ($user === null) {
            return null;
        }

        if ($info !== []) {
            $user->lastname = $info['lastname'];
            $user->firstname = $info['firstname'];
            $user->save();
        }

        return $user;
    }

    /**
     * Create or update user.
     *
     * @param array $info
     * @return mixed
     */
    protected function updateOrCreateUser(array $info)
    {
        $user = User::findByEmail($info['email']);

        if ($user) {
            return $this->updateUser($user->id, $info);
        } else {
            return $this->createUser($info);
        }
    }

    /**
     * Auth connexion creation.
     *
     * @param string $user_id
     * @param array  $info
     * @return mixed
     */
    public function addAuth(string $user_id, array $info)
    {
        return resolve($this->config['model'])::create(array_merge($info, [
            'user_id' => $user_id,
        ]));
    }

    /**
     * Auth connexion creation.
     *
     * @param string $user_id
     * @param array  $info
     * @return mixed
     */
    protected function createAuth(string $user_id, array $info=[])
    {
        return resolve($this->config['model'])::updateOrCreate([
            'user_id' => $user_id,
        ], array_merge($info, [
            'last_login_at' => new \DateTime(),
        ]));
    }

    /**
     * Update auth.
     *
     * @param string $user_id
     * @param array  $info
     * @return mixed
     */
    protected function updateAuth(string $user_id, array $info=[])
    {
        $userAuth = resolve($this->config['model'])::find($user_id);

        foreach ($info as $key => $value) {
            $userAuth->$key = $value;
        }

        $userAuth->save();

        return $userAuth;
    }

    /**
     * Connect a user.
     *
     * @param Request          $request
     * @param User             $user
     * @param \App\Models\Auth $userAuth
     * @return mixed
     */
    protected function connect(Request $request, User $user=null, \App\Models\Auth $userAuth=null)
    {
        // If everything is ok, connecting the user.
        if ($user && $userAuth) {
            if (!$user->is_active) {
                return $this->error($request, $user, $userAuth, 'Ce compte a été désactivé');
            }

            $user->timestamps = false;
            $user->last_login_at = new \DateTime();
            $user->save();

            $userAuth->timestamps = false;
            $userAuth->last_login_at = new \DateTime();
            $userAuth->save();

            Auth::guard('web')->login($user);
            \Session::put('auth_provider', $this->name);

            return $this->success($request, $user, $userAuth);
        } else {
            return $this->error($request, $user, $userAuth);
        }
    }

    /**
     * Redirect to the right page in case of success.
     *
     * @param Request          $request
     * @param User             $user
     * @param \App\Models\Auth $userAuth
     * @param string           $message
     * @return mixed
     */
    protected function success(Request $request, User $user=null, \App\Models\Auth $userAuth=null, string $message=null)
    {
        if ($message === null) {
            return redirect(\Session::get('url.intended', '/'));
        } else {
            return redirect(\Session::get('url.intended', '/'))->withSuccess($message);
        }
    }

    /**
     * Redirect to the right page in case of error.
     *
     * @param Request          $request
     * @param User             $user
     * @param \App\Models\Auth $userAuth
     * @param string           $message
     * @return mixed
     */
    protected function error(Request $request, User $user=null, \App\Models\Auth $userAuth=null, string $message=null)
    {
        if ($message === null) {
            return redirect()->route(
	            'login.show',
	            ['provider' => $this->name]
            )->withError('Il n\'a pas été possible de vous connecter')->withInput();
        } else {
            return redirect()->route('login.show', ['provider' => $this->name])->withError($message)->withInput();
        }
    }
}

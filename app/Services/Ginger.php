<?php
/**
 * Ginger Service.
 * Retrives infomations about UTC members.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services;

class Ginger
{
    protected const URL = 'https://assos.utc.fr/ginger/v1/';

    protected $user;
    protected $responseCode;
    protected $key;

    /**
     * Creates the class with the portal's default key.
     */
    public function __construct()
    {
        $this->key = config('app.ginger_key');
    }

    /**
     * Changes the used key.
     *
     * @param string $key
     * @return Ginger
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Retrieves from ginger information about a user corresponding to the given login.
     *
     * @param  string $login
     * @return Ginger
     */
    public function user(string $login)
    {
        $response = self::call(
            'GET',
            $login
        );

        $this->responseCode = $response === null ? null : $response->status;
        $this->user = $this->responseCode === 200 ? $response->content : null;

        return $this;
    }

    /**
     * Retrieves directly from Ginger trough a given email.
     *
     * @param  string $email
     * @return Ginger
     */
    public function userByEmail(string $email)
    {
        $response = self::call(
            'GET',
            'mail/'.$email
        );

        $this->responseCode = $response === null ? null : $response->status;
        $this->user = $this->responseCode === 200 ? $response->content : null;

        return $this;
    }

    /**
     * Retrieves directly from Ginger trough a given login.
     *
     * @param  string $login
     * @return array        Retourne l'ensemble des données de l'utilisateur.
     */
    public function getUser(string $login)
    {
        return self::user($login)->get();
    }

    /**
     * Retrieves directly from Ginger trough a given email.
     *
     * @param  string $email
     * @return array        Retourne l'ensemble des données de l'utilisateur.
     */
    public function getUserByEmail(string $email)
    {
        return self::userByEmail($email)->get();
    }

    /**
     * Indicates if user exists or not.
     *
     * @param  string $login
     * @return boolean
     */
    public function userExists(string $login)
    {
        $this->user($login);

        return $this->get() !== null;
    }

    /**
     * Same as previous functions but has to be used after searching a user.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->user !== null;
    }

    /**
     * Retrieves from Ginger the response code for a given login.
     *
     * @param  string $login
     * @return integer
     */
    public function responseCode(string $login)
    {
        $this->user($login);

        return $this->responseCode;
    }

    /**
     * Retrieves from Ginger a user contributions.
     *
     * @param  string $login
     * @return array
     */
    public function getContributions(string $login=null)
    {
        if (!$this->get()) {
            if ($login) {
                $this->user($login);
            }

            if (!$this->get()) {
                return [];
            }
        }

        $contributionList = $this->call(
            'GET',
            $this->getLogin().'/cotisations'
        )->content;
        $contributions = [];

        foreach ($contributionList as $contribution) {
            $contributions[] = $this->parseContribution($contribution);
        }

        return $contributions;
    }

    /**
     * Adds a contribution.
     *
     * @param string $begin
     * @param string $end
     * @param string $money
     * @return mixed
     */
    public function addContribution(string $begin, string $end, string $money)
    {
        if (!$this->get()) {
            return $this->parseContribution(null);
        }

        $response = $this->call(
            'POST',
            $this->getLogin().'/cotisations',
            [
                'debut' => $begin,
                'fin' => $end,
                'montant' => $money,
            ]
        );

        return $this->parseContribution($response->content);
    }

    /**
     * Parse a contribution to return it at the right format.
     *
     * @param  mixed $contribution
     * @return mixed
     */
    protected function parseContribution($contribution)
    {
        if ($contribution) {
            return new class($contribution) {
                /**
                 * Contribution creation.
                 *
                 * @param mixed $contribution
                 */
                public function __construct($contribution)
                {
                    $this->id = ($contribution->id ?? null);
                    $this->begin_at = ($contribution->debut ?? null);
                    $this->end_at = ($contribution->fin ?? null);
                    $this->money = ($contribution->montant ?? null);
                }
            };
        } else {
            return new class() {
                /**
                 * Null contribution creation.
                 */
                public function __construct()
                {
                    $this->id = null;
                    $this->begin_at = null;
                    $this->end_at = null;
                    $this->money = null;
                }
            };
        }
    }

    /**
     * Retrieves from Ginger the response code for a given login.
     *
     * @return integer|null
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * Renvoie l'utlisateur si existance de celui-ci.
     *
     * @return array|null
     */
    public function get()
    {
        return $this->user;
    }

    /**
     * Returns user's login if it exists.
     *
     * @return string|null
     */
    public function getLogin()
    {
        return ($this->user === null ? null : $this->user->login);
    }

    /**
     * Returns user's last name if it exists.
     *
     * @return string|null
     */
    public function getLastname()
    {
        return ($this->user === null ? null : $this->user->nom);
    }

    /**
     * Returns user's first name if it exists.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return ($this->user === null ? null : $this->user->prenom);
    }

    /**
     * Returns user's email if it exists.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return ($this->user === null ? null : $this->user->mail);
    }

    /**
     * Returns user's type if it exists.
     *
     * @return string|null
     */
    public function getType()
    {
        return ($this->user === null ? null : $this->user->type);
    }

    /**
     * Returns user's badge if it exists.
     *
     * @return string|null
     */
    public function getBadge()
    {
        return ($this->user === null ? null : $this->user->badge_uid);
    }

    /**
     * Returns if user is adult if it exists.
     *
     * @return boolean|null
     */
    public function isAdult()
    {
        return ($this->user === null ? null : $this->user->is_adulte);
    }

    /**
     * Indicates if a user is contributor if exists.
     *
     * @return boolean|null
     */
    public function isContributor()
    {
        return ($this->user === null ? null : $this->user->is_cotisant);
    }

    /**
     * Executes a curl request.
     *
     * @param  string $method
     * @param  string $route
     * @param  array  $params
     * @return object         Contains the response and also the HTTP code and some headers.
     */
    protected function call(string $method, string $route, array $params=[])
    {
        $curl = \Curl::to(self::URL.$route.'?key='.$this->key)
	        ->withData($params)
	        ->returnResponseObject();

        if (strpos(request()->getHttpHost(), 'utc.fr')) {
            $curl = $curl->withProxy('proxyweb.utc.fr', '3128');
        }

        if ($method === 'POST') {
            $response = $curl->post();
        } else {
            $response = $curl->asJson()->get();
        }

        return $response;
    }
}

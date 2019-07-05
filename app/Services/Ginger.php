<?php
/**
 * Service Ginger.
 * Permet de récupérer des informations concernant un membre de l'UTC
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
     * Crée la classe avec la clé du portail par défaut.
     */
    public function __construct()
    {
        $this->key = config('app.ginger_key');
    }

    /**
     * Change la clé utilisée.
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
     * Permet de récupérer auprès de Ginger un login précis.
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
     * Permet de récupérer directement auprès de Ginger via un email précis.
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
     * Permet de récupérer directement auprès de Ginger via un login précis.
     *
     * @param  string $login
     * @return array        Retourne l'ensemble des données de l'utilisateur.
     */
    public function getUser(string $login)
    {
        return self::user($login)->get();
    }

    /**
     * Permet de récupérer directement auprès de Ginger via un email précis.
     *
     * @param  string $email
     * @return array        Retourne l'ensemble des données de l'utilisateur.
     */
    public function getUserByEmail(string $email)
    {
        return self::userByEmail($email)->get();
    }

    /**
     * Indique si l'utilisateur existe ou non.
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
     * Identique à la fonction précédente mais à être utilisé après recherche d'un user.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->user !== null;
    }

    /**
     * Permet de récupérer auprès de Ginger le code de réponse pour un login précis.
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
     * Permet de récupérer auprès de Ginger les cotisations d'un utilisateur.
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
     * Ajoute une contribution.
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
     * Parse une contribution pour la retourner au bon format.
     *
     * @param  mixed $contribution
     * @return mixed
     */
    protected function parseContribution($contribution)
    {
        if ($contribution) {
            return new class($contribution) {
                /**
                 * Création de la contribution.
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
                 * Création d'un contribution null.
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
     * Permet de récupérer auprès de Ginger le code de réponse pour un login précis.
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
     * Renvoie le login de l'utilisateur si existant.
     *
     * @return string|null
     */
    public function getLogin()
    {
        return ($this->user === null ? null : $this->user->login);
    }

    /**
     * Renvoie le nom de l'utilisateur si existant.
     *
     * @return string|null
     */
    public function getLastname()
    {
        return ($this->user === null ? null : $this->user->nom);
    }

    /**
     * Renvoie le prenom de l'utilisateur si existant.
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return ($this->user === null ? null : $this->user->prenom);
    }

    /**
     * Renvoie le mail de l'utilisateur si existant.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return ($this->user === null ? null : $this->user->mail);
    }

    /**
     * Renvoie le type de l'utilisateur si existant.
     *
     * @return string|null
     */
    public function getType()
    {
        return ($this->user === null ? null : $this->user->type);
    }

    /**
     * Renvoie le badge de l'utilisateur si existant.
     *
     * @return string|null
     */
    public function getBadge()
    {
        return ($this->user === null ? null : $this->user->badge_uid);
    }

    /**
     * Indique si l'utilisateur est adulte si existant.
     *
     * @return boolean|null
     */
    public function isAdult()
    {
        return ($this->user === null ? null : $this->user->is_adulte);
    }

    /**
     * Indique si l'utilisateur est contisant si existant.
     *
     * @return boolean|null
     */
    public function isContributor()
    {
        return ($this->user === null ? null : $this->user->is_cotisant);
    }

    /**
     * Exécute la requête via Curl.
     *
     * @param  string $method
     * @param  string $route
     * @param  array  $params
     * @return object         Contient la réponse mais aussi le code HTTP et quelques headers
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

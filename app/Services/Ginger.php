<?php

namespace App\Services;

use Curl;

/**
 * Cette classe permet de récupérer des informations concernant un membre de l'UTC
 */
class Ginger {
	protected const URL = 'https://assos.utc.fr/ginger/v1/';
	protected $user;
	protected $responseCode;
	protected $key;

	/**
	 * Crée la classe avec la clé du portail par défaut
	 */
	public function __construct() {
		$this->key = config('app.ginger_key');
	}

	/**
	 * Change la clé utilisée
	 * @param string $key Clé Ginger donnée
	 */
	public function setKey($key) {
		$this->key = $key;

		return $this;
	}

	/**
	 * Permet de récupérer auprès de Ginger un login précis
	 * @param  string $login Login UTC
	 * @return Ginger        Objet Ginger pour singleton
	 */
	public function user($login) {
		$response = self::call(
			'GET',
			$login
		);

		$this->responseCode = $response === null ? null : $response->status;
		$this->user = $this->responseCode === 200 ? $response->content : null;

		return $this;
	}

	/**
	 * Permet de récupérer directement auprès de Ginger un login précis
	 * @param  string $login Login UTC
	 * @return array        Retourne l'ensemble des données de l'utilisateur
	 */
	public function getUser($login) {
		$response = self::call(
			'GET',
			$login
		);

		$this->responseCode = $response === null ? null : $response->status;

	 	return $this->responseCode === 200 ? $response->content : null;
	}

	/**
	 * Indique si l'utilisateur existe ou non
	 * @param  string $login Login UTC
	 * @return boolean        Existance ou non
	 */
	public function userExists($login) {
		$this->user($login);

		return $this->user !== null;
	}

	/**
	 * Identique à la fonction précédente mais à être utilisé après recherche d'un user
	 * @return boolean Existance ou non
	 */
	public function exists() {
		return $this->user !== null;
	}

	/**
	 * Permet de récupérer auprès de Ginger le code de réponse pour un login précis
	 * @param  string $login Login UTC
	 * @return int        Retourne la code de réponse
	 */
	public function responseCode($login) {
		$this->user($login);

		return $this->responseCode;
	}

	/**
	 * Permet de récupérer auprès de Ginger le code de réponse pour un login précis
	 * @return int        Retourne la code de réponse
	 */
	public function getResponseCode() {
		return $this->responseCode;
	}

	/**
	 * Renvoie l'utlisateur si existance de celui-ci
	 * @return array
	 */
	public function get() {
		return $this->user;
	}

	/**
	 * Renvoie le login de l'utilisateur si existant
	 * @return string Ressource demandée
	 */
	public function getLogin() {
		return ($this->user === null ? null : $this->user->login);
	}

	/**
	 * Renvoie le nom de l'utilisateur si existant
	 * @return string Ressource demandée
	 */
	public function getLastname() {
		return ($this->user === null ? null : $this->user->nom);
	}

	/**
	 * Renvoie le prenom de l'utilisateur si existant
	 * @return string Ressource demandée
	 */
	public function getFirstname() {
		return ($this->user === null ? null : $this->user->prenom);
	}

	/**
	 * Renvoie le mail de l'utilisateur si existant
	 * @return string Ressource demandée
	 */
	public function getEmail() {
		return ($this->user === null ? null : $this->user->mail);
	}

	/**
	 * Renvoie le type de l'utilisateur si existant
	 * @return string Ressource demandée
	 */
	public function getType() {
		return ($this->user === null ? null : $this->user->type);
	}

	/**
	 * Renvoie le badge de l'utilisateur si existant
	 * @return string Ressource demandée
	 */
	public function getBadge() {
		return ($this->user === null ? null : $this->user->badge_uid);
	}

	/**
	 * Indique si l'utilisateur est adulte si existant
	 * @return boolean Ressource demandée
	 */
	public function isAdult() {
		return ($this->user === null ? null : $this->user->is_adulte);
	}

	/**
	 * Indique si l'utilisateur est contisant si existant
	 * @return boolean Ressource demandée
	 */
	public function isContributor() {
		return ($this->user === null ? null : $this->user->is_cotisant);
	}

	/**
	 * Exécute la requête via Curl
	 * @param  string $method Verbe à utiliser pour la requête
	 * @param  string $route  Route vers laquelle pointer
	 * @param  array  $params Paramètres à envoyer
	 * @return object         Contient la réponse mais aussi le code HTTP et quelques headers
	 */
	protected function call($method, $route, $params = []) {
		$curl = Curl::to(self::URL.$route.'?key='.$this->key)
			->withData($params)
			->asJson()
			->returnResponseObject();

		if (strpos($_SERVER['HTTP_HOST'], 'utc.fr'))
			$curl = $curl->withProxy('proxyweb.utc.fr', 3128);

		if ($method === 'POST')
			$response = $curl->post();
		else
			$response = $curl->get();

		return $response;
	}
}

<?php
/**
 * Méthodes pour manipuler scopes et token.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Service;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use App\Models\Client;
use App\Exceptions\PortailException;

trait TokenUtils
{
    /**
     * Renvoie le scope et ses parents ou ses hérédités (prend en compte l'héridité des verbes).
     *
     * @param string  $scope
     * @param boolean $goUp  Permet de spécifier dans quel sens de l'héridité à générer.
     * @return array
     */
    public function getRelatives(string $scope=null, bool $goUp=false)
    {
        if ($scope === null) {
            return $this->all();
        }

        $current = $this->find($scope);

        if ($current === [] || $current === null) {
            return [];
        }

        $scopes = [
            $scope => $current[$scope]['description'],
        ];

        $elements = explode('-', $scope);

        if ($goUp) {
            for ($i = (count($elements) - 1); $i > 2; $i--) {
                array_pop($elements);
                $scopes = array_merge($scopes, $this->getRelatives(implode('-', $elements), $goUp));
            }

            $elements = explode('-', $scope);
        } else if (isset($current[$scope]['scopes'])) {
            $scopes = array_merge($scopes, $this->generate($scope, $current[$scope]['scopes']));
        }

        $nextVerbs = $this->nextVerbs($elements[1], $goUp);

        if ($nextVerbs !== []) {
            foreach ($nextVerbs as $nextVerb) {
                $elements[1] = $nextVerb;
                $scopes = array_merge($scopes, $this->getRelatives(implode('-', $elements), $goUp));
            }
        }

        return $scopes;
    }

    /**
     * Retourne si le token est du type User.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isUserToken(Request $request)
    {
        return $request->user() !== null || $this->getToken($request)->transient();
    }

    /**
     * Retourne si le token est du type Transient.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isTransient(Request $request)
    {
        return $this->getToken($request)->transient();
    }

    /**
     * Retourne si le token est du type Client ou si le token est transient et de type client.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isClientToken(Request $request)
    {
        return $request->user() === null;
    }

    /**
     * Récupérer le type de token (sert pour connaitre le header des scopes).
     *
     * @param  Request $request
     * @return string  'client' / 'user'
     */
    public function getTokenType(Request $request)
    {
        return $this->getToken($request) ? ($this->isClientToken($request) ? 'client' : 'user') : null;
    }

    /**
     * Indique s'il s'agit d'un token user ou client.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isUserOrClientToken(Request $request)
    {
        if ($request->user() === null) {
            $bearerToken = $request->bearerToken();
            $tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');
            $token = Token::find($tokenId);

            if ($token !== null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retourne le token.
     *
     * @param  Request $request
     * @return mixed
     */
    public function getToken(Request $request)
    {
        if ($request->user()) {
            return $request->user()->token();
        } else {
            try {
                $bearerToken = $request->bearerToken();
                $tokenId = (new Parser())->parse($bearerToken)->getHeader('jti');

                return Token::find($tokenId);
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * Retourne le client Oauth de la requête.
     *
     * @param  Request $request
     * @return mixed
     */
    public function getClient(Request $request)
    {
        $clientFromPassport = $this->getToken($request)->client;

        return $clientFromPassport ? Client::find($clientFromPassport->id) : null;
    }

    /**
     * Indique si la requête est OAuth.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isOauthRequest(Request $request)
    {
        return (bool) $this->getToken($request);
    }

    /**
     * Retourne les Middleware à utiliser pour accéder à une route en matchant le scope ou les scopes.
     *
     * @param  Request $request
     * @param  mixed   $scopes
     * @return boolean
     */
    public function has(Request $request, $scopes)
    {
        return is_array($scopes) ? $this->hasAll($request, $scopes) : $this->hasOne($request, [$scopes]);
    }

    /**
     * Retourne si on peut accéder à une route en matchant au moins un scope parmi la liste.
     *
     * @param  Request $request
     * @param mixed   $scopes
     * @return boolean
     */
    public function hasOne(Request $request, $scopes=[])
    {
        if (is_array($scopes)) {
            $scopes = $this->getMatchingScopes($scopes);
        } else {
            $scopes = $this->getMatchingScopes([$scopes]);
        }

        $token = $this->getToken($request);

        if ($token === null) {
            return false;
        }

        if ($token->transient()) {
            foreach ($scopes as $scope) {
                if (strpos($scope, 'user') === 0) {
                    return true;
                }
            }

            return false;
        }

        foreach ($token->scopes as $scope) {
            if (in_array($scope, $scopes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne si on peut accéder à une route en matchant tous les scopes parmi la liste.
     *
     * @param Request $request
     * @param array   $scopes
     * @return boolean
     */
    public function hasAll(Request $request, array $scopes=[])
    {
        $scopes = $this->getMatchingScopes($scopes);
        $token = $this->getToken($request);

        if ($token === null) {
            return false;
        }

        if ($token->transient()) {
            foreach ($scopes as $scope) {
                if (strpos($scope, 'user') !== 0) {
                    return false;
                }
            }

            return true;
        }

        foreach ($token->scopes as $scope) {
            if (!in_array($scope, $scopes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Génère une exception si les scopes ne sont correspondent pas au bon type d'authentification.
     *
     * @param  array  $scopes
     * @param  string $grantType
     * @return mixed
     */
    public function checkScopesForGrantType(array $scopes, string $grantType=null)
    {
        $middleware = null;

        foreach ($scopes as $scope) {
            $elements = explode('-', $scope);

            if (is_null($middleware)) {
                $middleware = $elements[0];
            } else if ($middleware !== $elements[0]) {
                throw new PortailException('Les scopes ne sont pas définis avec les mêmes types d\'authentification !');
                // Des scopes commençant par c- et u-.
            }
        }

        if ($middleware && (($middleware === 'client' && $grantType !== 'client_credentials')
        	|| ($grantType === 'client_credentials' && $middleware !== 'client'))) {
            throw new PortailException('Les scopes ne sont pas définis pour le bon type d\'authentification !');
            // Des scopes commençant par c- et u-.
        }
    }
}

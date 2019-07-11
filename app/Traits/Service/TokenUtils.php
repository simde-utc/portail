<?php
/**
 * Methods to handle scopes and tokens.
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
     * Returns the scope and its parents or all childs (including verb inheritance).
     *
     * @param string  $scope
     * @param boolean $goUp  Specify the inheritance direction.
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
     * Returns if a given token is a User token or not.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isUserToken(Request $request)
    {
        return $this->isOauthRequest($request) && ($request->user() !== null || $this->getToken($request)->transient());
    }

    /**
     * Returns if a given token is a Transient token or not.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isTransient(Request $request)
    {
        return $this->isOauthRequest($request) && $this->getToken($request)->transient();
    }

    /**
     * Returns if a given token is a Client token or if it is a Transient and Client token.
     *
     * @param  Request $request
     * @return boolean
     */
    public function isClientToken(Request $request)
    {
        return $this->isOauthRequest($request) && $request->user() === null;
    }

    /**
     * Retrieves the type of a given token (useful to know the header of the scopes)
     *
     * @param  Request $request
     * @return string  'client' / 'user'
     */
    public function getTokenType(Request $request)
    {
        return $this->getToken($request) ? ($this->isClientToken($request) ? 'client' : 'user') : null;
    }

    /**
     * Indicates if it's a User or Client token.
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
     * Returns the token.
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
     * Returns the Request's OAuth client. 
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
     * Idicates if the request is an OAuth request or not. 
     *
     * @param  Request $request
     * @return boolean
     */
    public function isOauthRequest(Request $request)
    {
        return (bool) $this->getToken($request);
    }

    /**
     * Returns Middlewares to use to access a route by matching one or several scopes.
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
     * Returns if we can access a route by matching at least a scope in the list. 
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
     * Returns if we can access a route by matchnig all scopes in the list.
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
     * Generates an exception if scopes don't match the right authentification type
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
                // Scopes starting by c- and u-.
            }
        }

        if ($middleware && (($middleware === 'client' && $grantType !== 'client_credentials')
        	|| ($grantType === 'client_credentials' && $middleware !== 'client'))) {
            throw new PortailException('Les scopes ne sont pas définis pour le bon type d\'authentification !');
            // Scopes starting by c- and u-.
        }
    }
}

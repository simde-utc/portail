<?php
/**
 * Methods to identify and check scopes.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Service;

use App\Exceptions\PortailException;

trait ScopesIdentification
{
    /**
     * This function enables to find the smallest scopes of a given scope.
     * (Really useful to list minimum scopes in controllers).
     *
     * @param  string|array $scope
     * @return array
     */
    public function getDeepestChildren($scope)
    {
        if (is_array($scope)) {
            return array_merge(...array_map(function ($one) {
                return $this->getDeepestChildren($one);
            }, $scope));
        }

        $find = $this->find($scope);
        if (!isset($find[$scope])) {
            throw new PortailException('Scope '.$scope.' non trouvé');
        }

        $current = $find[$scope];
        $deepestChildren = [];
        if ($current === [] || $current === null) {
            return [];
        }

        if (!isset($current['scopes']) || count($current['scopes']) === 0) {
            return [$scope];
        }

        foreach ($current['scopes'] as $child => $data) {
            $deepestChildren = array_merge(
                $deepestChildren,
                $this->getDeepestChildren($scope.'-'.$child)
            );
        }

        return $deepestChildren;
    }

    /**
     * Return the scopes list and its parents (considering verbs inheritance).
     *
     *
     * @param  array  $scopes
     * @param  string $middleware
     * @return array
     */
    public function getMatchingScopes(array $scopes=[], string $middleware=null)
    {
        if ($scopes === []) {
            throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser \
                matchAny([bool $canBeUser = true, bool $canBeClient = true])');
        }

        $matchingScopes = [];
        foreach ($scopes as $scope) {
            if ($scope === null) {
                throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser \
                    matchAny([bool $canBeUser = true, bool $canBeClient = true])');
            }

            $elements = explode('-', $scope);
            if (!isset($middleware)) {
                $middleware = $elements[0];
            } else if ($middleware !== $elements[0]) {
                throw new PortailException('Les scopes ne sont pas définis avec les mêmes types d\'authentification !');
                // Scopes starting by c- and u-.
            }

            $current = $this->getRelatives($scope, true);
            if ($current === []) {
                throw new PortailException('Le scope '.$scope.' n\'existe pas !');
            }

            $matchingScopes = array_merge($matchingScopes, $current);
        }

        return array_keys($matchingScopes);
    }

    /**
     * Returns authentification middlewares.
     *
     * @param  array   $userScopes
     * @param  array   $clientScopes
     * @param  boolean $matchOne
     * @return array
     */
    private function matchAny(array $userScopes=[], array $clientScopes=[], bool $matchOne=true)
    {
        $indicator = $matchOne ? '1' : '0';
        if (count($userScopes) > 0) {
            if (count($clientScopes) > 0) {
                $middleware = $this->getAuthMiddleware().'.any:'.implode(',',
                    [$indicator, implode('|', $userScopes), implode('|', $clientScopes)]);
            } else {
                $middleware = $this->getAuthMiddleware().'.user:'.implode(',', [$indicator, implode('|', $userScopes)]);
            }
        } else if (count($clientScopes) > 0) {
            $middleware = $this->getAuthMiddleware().'.client:'.implode(',', [$indicator, implode('|', $clientScopes)]);
        } else {
            return [];
        }

        return [
            $middleware,
        ];
    }

    /**
     * Retuns authentification middlewares for every client connected to a user.
     *
     * @return array
     */
    public function matchAnyUser()
    {
        return [
            $this->getAuthMiddleware().'.user',
        ];
    }

    /**
     * Retuns authentification middlewares for every client not connected to a user.
     *
     * @return array
     */
    public function matchAnyClient()
    {
        return [
            $this->getAuthMiddleware().'.client',
        ];
    }

    /**
     * Retuns authentification middlewares for every client connected or not to a user.
     *
     * @return array
     */
    public function matchAnyUserOrClient()
    {
        return [
            $this->getAuthMiddleware().'.any',
        ];
    }

    /**
     * Returns middlewares to use to access a route by matching a scopes or several scopes.
     *
     * @param  string|array $userScopes   Scopes list or User/Client scopes list if accessor is a User/Client.
     * @param  array        $clientScopes User/Client scope list to have.
     * @return array
     */
    public function match($userScopes, array $clientScopes=[])
    {
        if (is_array($userScopes)) {
            return $this->matchAll($userScopes, $clientScopes);
        } else {
            array_push($clientScopes, $userScopes);

            return $this->matchOne($clientScopes);
        }
    }

    /**
     * Returns all widdlewares to use to access a route by matching at least one scope in the list.
     *
     * @param string|array $userScopes
     * @param string|array $clientScopes
     * @return array
     */
    public function matchOne($userScopes=[], $clientScopes=[])
    {
        $userScopes = !is_array($userScopes) ? [$userScopes] : $userScopes;
        $clientScopes = !is_array($clientScopes) ? [$clientScopes] : $clientScopes;

        if (count($userScopes) == 0) {
            throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser \
                matchAny([bool $canBeUser = true, bool $canBeClient = true])');
        }

        if (explode('-', $userScopes[0])[0] === 'user') {
            return $this->matchAny($userScopes, $clientScopes);
        } else {
            return $this->matchAny($clientScopes, $userScopes);
        }
    }

    /**
     * Returns all middleware to use to access a routes by matching all scopes or their parents.
     *
     * @param array $userScopes
     * @param array $clientScopes
     * @return array
     */
    public function matchAll(array $userScopes=[], array $clientScopes=[])
    {
        if (count($userScopes) == 0) {
            throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser \
                matchAny([bool $canBeUser = true, bool $canBeClient = true])');
        }

        if (explode('-', $userScopes[0])[0] === 'user') {
            return $this->matchAny($userScopes, $clientScopes, false);
        } else {
            return $this->matchAny($clientScopes, $userScopes, false);
        }
    }

    /**
     * Creates the middleware to check that a scope owns one of the given scopes smallest children.
     *
     * @param  string|array $userScope
     * @param  string|array $clientScope
     * @return array
     */
    public function matchOneOfDeepestChildren($userScope=null, $clientScope=null)
    {
        return $this->matchOne(
            $userScope ? $this->getDeepestChildren($userScope) : null,
            $clientScope ? $this->getDeepestChildren($clientScope) : null
        );
    }
}

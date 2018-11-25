<?php
/**
 * Méthodes pour identifier et vérifier les scopes.
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
     * Cette fonction permet de retrouver les plus petits scopes du scope donné.
     * (très utile pour lister les scopes minimum dans les controlleurs).
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
     * Retourne la liste des scopes et des ses parents (prise en compte de l'héridité des verbes).
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
                // Des scopes commençant par c- et u-.
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
     * Retourne les middlewares d'authentification.
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
            $this->getAuthMiddleware().'.check',
        ];
    }

    /**
     * Retourne les middlewares d'authentification pour tout client connecté à un utilisateur.
     *
     * @return array
     */
    public function matchAnyUser()
    {
        return [
            $this->getAuthMiddleware().'.user',
            $this->getAuthMiddleware().'.check'
        ];
    }

    /**
     * Retourne les middlewares d'authentification pour tout client non connecté à un utilisateur.
     *
     * @return array
     */
    public function matchAnyClient()
    {
        return [
            $this->getAuthMiddleware().'.client',
            $this->getAuthMiddleware().'.check'
        ];
    }

    /**
     * Retourne les middlewares d'authentification pour tout client connecté ou non à un utilisateur.
     *
     * @return array
     */
    public function matchAnyUserOrClient()
    {
        return [
            $this->getAuthMiddleware().'.any',
            $this->getAuthMiddleware().'.check'
        ];
    }

    /**
     * Retourne les middlewares à utiliser pour accéder à une route en matchant le scope ou les scopes.
     *
     * @param  string|array $userScopes   Liste des scopes ou des scopes user/client à avoir si on est user/client.
     * @param  array        $clientScopes Liste des scopes client/user à avoir.
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
     * Retourne les middlewares à utiliser pour accéder à une route en matchant au moins un scope parmi la liste.
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
     * Retourne les middlewares à utiliser pour accéder à une route en matchant tous les scopes ou leurs parents de la liste.
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
     * Crée le middleware pour vérifier qu'un scope possède au moins un des plus petits enfants des scopes donnés.
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

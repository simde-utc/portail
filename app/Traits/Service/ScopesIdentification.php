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

trait ScopesIdentification
{
    /**
     * Cette fonction permet de retrouver les plus petits scopes du scope donné
     * (très utile pour lister les scopes minimum dans les controlleurs)
     * @param  string/array $scope
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
     * Retourne la liste des scopes et des ses parents (prise en compte de l'héridité des verbes)
     *
     * @param array $scopes
     * @return array
     */
    public function getMatchingScopes(array $scopes=[], bool $checkMiddleware=true, string $middleware=null)
    {
        if ($scopes === [] || $scopes === null) {
            throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser matchAny([bool $canBeUser = true, bool $canBeClient = true])');
        }

        $matchingScopes = [];
        foreach ($scopes as $scope) {
            if ($scope === null) {
                throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser matchAny([bool $canBeUser = true, bool $canBeClient = true])');
            }

            $elements = explode('-', $scope);
            if (!isset($middleware)) {
                $middleware = $elements[0];
            } else if ($middleware !== $elements[0] && $checkMiddleware) {
                throw new PortailException('Les scopes ne sont pas définis avec les mêmes types d\'authentification !');
                // Des scopes commençant par c- et u-
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
     * Retourne les Middleware d'authentification
     *
     * @param boolean $userMustBeConnected
     * @return array
     */
    private function matchAny(array $userScopes=[], array $clientScopes=[], bool $matchOne=true)
    {
        $indicator = $matchOne ? '1' : '0';
        if (count($userScopes) > 0) {
            if (count($clientScopes) > 0) {
                $middleware = $this->getAuthMiddleware().'.any:'.implode(',', [$indicator, implode('|', $userScopes), implode('|', $clientScopes)]);
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
     * Retourne les Middleware d'authentification pour tout client connecté à un utilisateur
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
     * Retourne les Middleware d'authentification pour tout client non connecté à un utilisateur
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
     * Retourne les Middleware d'authentification pour tout client connecté ou non à un utilisateur
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
     * Retourne les Middleware à utiliser pour accéder à une route en matchant le scope ou les scopes
     * @param  string/array $scopes  Liste des scopes ou des scopes user/client à avoir si on est user/client
     * @param  array        $scopes2 Liste des scopes client/user à avoir
     * @return array
     */
    public function match($scopes, array $scopes2=[])
    {
        if (is_array($scopes)) {
            return $this->matchAll($scopes, $scopes2);
        } else {
            array_push($scopes2, $scopes);
            return $this->matchOne($scopes2);
        }
    }

    /**
     * Retourne les Middleware à utiliser pour accéder à une route en matchant au moins un scope parmi la liste
     *
     * @param string/array $scopes
     * @return array
     */
    public function matchOne($scopes=[], $scopes2=[])
    {
        $scopes = !is_array($scopes) ? [$scopes] : $scopes;
        $scopes2 = !is_array($scopes2) ? [$scopes2] : $scopes2;
        if (count($scopes) == 0) {
            throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser matchAny([bool $canBeUser = true, bool $canBeClient = true])');
        }

        if (explode('-', $scopes[0])[0] === 'user') {
            return $this->matchAny($scopes, $scopes2);
        } else {
            return $this->matchAny($scopes2, $scopes);
        }

        return $this->matchAny($scopes, $scopes2);
        return $this->matchAny($middleware !== 'client', $middleware !== 'user', $scopeList);
    }

    /**
     * Retourne les Middleware à utiliser pour accéder à une route en matchant tous les scopes ou leurs parents de la liste
     *
     * @param string/array $scopes
     * @return array
     */
    public function matchAll(array $scopes=[], array $scopes2=[])
    {
        if (count($scopes) == 0) {
            throw new PortailException('Il est nécessaire de définir au moins un scope ou d\'utiliser matchAny([bool $canBeUser = true, bool $canBeClient = true])');
        }

        if (explode('-', $scopes[0])[0] === 'user') {
            return $this->matchAny($scopes, $scopes2, false);
        } else {
            return $this->matchAny($scopes2, $scopes, false);
        }
    }

    /**
     * Crée le middleware pour vérifier qu'un scope possède au moins un des plus petits enfants des scopes donnés
     * @param  string/array $scope
     * @param  string/array $scopes2
     */
    public function matchOneOfDeepestChildren($scope=null, $scope2=null)
    {
        return $this->matchOne(
        $scope ? $this->getDeepestChildren($scope) : null,
        $scope2 ? $this->getDeepestChildren($scope2) : null
        );
    }
}

<?php
/**
 * Scopes Service.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Services;

use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Laravel\Passport\Token;
use App\Models\Client;
use App\Exceptions\PortailException;
use App\Traits\Service\TokenUtils;
use App\Traits\Service\ScopesIdentification;

class Scopes
{
    use TokenUtils, ScopesIdentification;

    /*
     * Scopes list depending on routes.
     *   - Scopes definition:
     *       `baseScope-verb-category` + (for each subcategory: `-subCategoryName`).
     *       For example : `user-get-info`, `user-get-assos`, `user-get-assos-followed-now`
     *
     *   - Base scopes definition:
     *      - **user** : An authentificated user is needed.
     *      - **client** :  The application needs to have application rights independently of a user.

     *   - Verb definition.
     *      Actions have a classification and inherit their parents' rights.
     *      - **manage**:  Whole resource management
     *          + **set** :  Possibility to write, update and delete data
     *              * **create**:  Create the associated data
     *              * **edit**:    Update the associated data
     *              * **remove**:  Delete the associated data
     *          + **get** :  Read-only retrievement.
     */

    protected $scopes;

    protected $allowPublic = false;

    /**
     * Retrieve the scopes' configuration.
     */
    public function __construct()
    {
        $this->scopes = config('scopes');
    }

    /**
     * Define if all routes are reachable.
     *
     * @param boolean $allow
     * @return Scopes
     */
    public function allowPublic(bool $allow=true)
    {
        $this->allowPublic = $allow;

        return $this;
    }

    /**
     * Define the middleware to call for authentication.
     *
     * @return string
     */
    protected function getAuthMiddleware()
    {
        $middleware = $this->allowPublic ? 'auth.public' : 'auth';
        $this->allowPublic = false;

        return $middleware;
    }

    /**
     * Generate scope and child scopes (with verb inheritance).
     *
     * @param  string $before
     * @param  array  $subScopes
     * @return array
     */
    private function generate(string $before, array $subScopes)
    {
        $scopes = [];

        foreach ($subScopes as $name => $data) {
            $prefix = $before.'-'.$name;

            if (isset($data['scopes'])) {
                $scopes = array_merge($scopes, $this->generate($prefix, $data['scopes']));
            }

            try {
                $scopes[$prefix] = $data['description'];
            } catch (\Exception $e) {
                throw new PortailException('Mauvaise définition (description) du scope '.$prefix);
            }
        }

        return $scopes;
    }

    /**
     * Return all scopes and child scopes (with verbs inheritance).
     *
     * @return array
     */
    public function all()
    {
        $scopes = [];

        foreach ($this->scopes as $type => $categories) {
            foreach ($categories as $name => $categorie) {
                foreach ($categorie['verbs'] as $verb => $data) {
                    $prefix = $type.'-'.$verb.'-'.$name;

                    try {
                        if (isset($data['scopes'])) {
                            $scopes = array_merge($scopes, $this->generate($prefix, $data['scopes']));
                        }

                        $scopes[$prefix] = $data['description'];
                    } catch (PortailException $e) {
                        throw new PortailException('Le scope '.$prefix.' est mal défini !');
                    }
                }
            }
        }

        return $scopes;
    }

    /**
     * Return scopes (must exist) with their description by category.
     *
     * @return array
     */
    public function getAllByCategories()
    {
        $categories = [];
        foreach ($this->all() as $scope => $description) {
            $elements = explode('-', $scope);

            if (!isset($categories[$elements[2]]) || !isset($categories[$elements[2]]['scopes'])) {
                $categorie = $this->scopes[$elements[0]][$elements[2]];

                $categories[$elements[2]] = [
                    'description' => $categorie['description'],
                    'scopes' => [
                        $scope => $description,
                    ]
                ];
            } else {
                $categories[$elements[2]]['scopes'][$scope] = $description;
            }
        }

        return $categories;
    }

    /**
     * Give the verbe which follows on the way down or up in the inheritance chain
     *
     * @param  string  $verb
     * @param  boolean $goUp
     * @return array        List of following verbs.
     */
    private function nextVerbs(string $verb, bool $goUp=false)
    {
        if ($goUp) {
            switch ($verb) {
                case 'get':
                case 'set':
                    return ['manage'];
                 break;

                case 'create':
                case 'edit':
                case 'remove':
                    return ['set'];
                 break;

                default:
                    return [];
            }
        } else {
            switch ($verb) {
                case 'manage':
                    return ['get', 'set'];
                 break;

                case 'set':
                    return ['create', 'edit'];
                 break;

                default:
                    return [];
            }
        }
    }

    /**
     * Find the existing scope (Must exist) and its descendants.
     *
     * @param  string $scope
     * @return array|null
     */
    protected function find(string $scope)
    {
        $elements = explode('-', $scope);

        if (count($elements) < 3) {
            throw new PortailException('Le scope '.$scope.' est incorrect
				et doit au moins posséder un système d\'authentification, un verbe et une catégorie');
        }

        if (!isset($this->scopes[$elements[0]][$elements[2]]['verbs'][$elements[1]])) {
            return [];
        }

        $current = $this->scopes[$elements[0]][$elements[2]]['verbs'][$elements[1]];
        for ($i = 3; $i < count($elements); $i++) {
            if (!isset($current['scopes'][$elements[$i]])) {
                return [];
            }

            $current = $current['scopes'][$elements[$i]];
        }

        if ($current === [] || !isset($current['description'])) {
            throw new PortailException('Le scope '.$scope.' est mal défini dans le fichier de config');
        } else {
            return [
                $scope => $current,
            ];
        }
    }

    /**
     * Return the scope (Must exist !) with its description.
     *
     * @param  string $scope
     * @return array  scope => description
     */
    public function get(string $scope)
    {
        $current = $this->find($scope);

        if ($current === [] || $current === null) {
            return [];
        }

        return [
            $scope => $current[$scope]['description'],
        ];
    }

    /**
     * Returns all scopes with their description by category.
     *
     * @param  array $scopes
     * @return array
     */
    public function getByCategories(array $scopes)
    {
        $categories = [];

        if ($scopes === []) {
            return [];
        }

        foreach ($scopes as $scope) {
            $elements = explode('-', $scope);

            if (!isset($middleware)) {
                $middleware = $elements[0];
            } else if ($middleware !== $elements[0]) {
                // Scopes starting by client- and user-.
                throw new PortailException('Les scopes ne sont pas définis avec les mêmes types d\'authentification !');
            }

            $current = $this->get($scope);

            if ($current === []) {
                throw new PortailException('Le scope '.$scope.' n\'existe pas !');
            }

            if (!isset($categories[$elements[2]]) || !isset($categories[$elements[2]]['scopes'])) {
                $categorie = $this->scopes[$middleware][$elements[2]];

                $categories[$elements[2]] = [
                    'description' => $categorie['description'],
                    'scopes' => [
                        $current[$scope]
                    ]
                ];

                if ($elements[0] == "user") {
                    $categories[$elements[2]]['icon'] = $categorie['icon'];
                }
            } else {
                array_push($categories[$elements[2]]['scopes'], $current[$scope]);
            }
        }

        return $categories;
    }

    /**
     *
     * Returns development scopes.
     *
     * @return array
     */
    public function getDevScopes()
    {
        $scopes = [
            'user-get-access',
        // No manage.
        ];

        foreach (array_keys(self::all()) as $scope) {
            if (substr($scope, 0, 11) === 'user-manage') {
                $scopes[] = $scope;
            }
        }

        return $scopes;
    }
}

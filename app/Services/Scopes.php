<?php
/**
 * Service Scopes.
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
     * Liste des scopes en fonction des routes.
     *   - Définition des scopes:
     *   	portée + "-" + verbe + "-" + categorie + (pour chaque sous-catégorie: '-' + sous-catégorie)
     *   	ex: user-get-user user-get-user-assos user-get-user-assos-followed
     *
     *   - Définition de la portée des scopes:
     *     + user :    user_credential => nécessite que l'application soit connecté à un utilisateur
     *     + client :  client_credential => nécessite que l'application est les droits d'application indépendante d'un utilisateur
     *
     *   - Définition du verbe:
     *     + manage:  gestion de la ressource entière
     *       + get :  récupération des informations en lecture seule
     *       + set :  posibilité d'écrire et modifier les données
     *         + create:  créer une donnée associée
     *         + edit:    modifier une donnée
     *         + remove:  supprimer une donnée
     */

    protected $scopes;

    protected $allowPublic = false;

    /**
     * Récupère la configuration des scopes.
     */
    public function __construct()
    {
        $this->scopes = config('scopes');
    }

    /**
     * Cette méthode permet de définir si les routes sont accessibles.
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
     * Cette méthode définie le middleware à appeler.
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
     * Génère le scope et les hérédités.
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
     * Renvoie tous les scopes et les hérédités.
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
     * Renvoie les scopes (doivent exister !) avec leur description par catégorie.
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
     * Donne le verbe qui suit par héridité montante ou descendante.
     *
     * @param  string  $verb
     * @param  boolean $goUp
     * @return array        Liste des verbes à suivre.
     */
    private function nextVerbs(string $verb, bool $goUp=false)
    {
        if ($goUp) {
            switch ($verb) {
                case 'set':
                case 'remove':
                    return ['manage'];
                 break;

                case 'get':
                case 'create':
                case 'edit':
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
     * Recherche le scope existant (qui doit exister) et sa descendance.
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
     * Renvoie le scope (doit exister !) avec sa description.
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
     * Renvoie les scopes (doivent exister !) avec leur description par catégorie.
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
                // Des scopes commençant par c- et u-.
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
                    'icon' => $categorie['icon'],
                    'scopes' => [
                        $current[$scope]
                    ]
                ];
            } else {
                array_push($categories[$elements[2]]['scopes'], $current[$scope]);
            }
        }

        return $categories;
    }

    /**
     * Retourne les scopes pour le développement.
     *
     * @return array
     */
    public function getDevScopes()
    {
        $scopes = [];

        foreach (array_keys(self::all()) as $scope) {
            if (substr($scope, 0, 11) === 'user-manage') {
                $scopes[] = $scope;
            }
        }

        return $scopes;
    }
}

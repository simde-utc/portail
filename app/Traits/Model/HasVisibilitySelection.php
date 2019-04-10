<?php
/**
 * Ajoute un sélecteur concernant les visibilités.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Model;

use App\Models\{
    User, Visibility
};
use App\Exceptions\PortailException;
use Illuminate\Database\Eloquent\Builder;

trait HasVisibilitySelection
{
    // Pour la visibilité on a besoin de connaître l'utilisateur sur lequel appliquer.
    protected static $userForVisibility;

    /**
     * Scope spécifique concernant l'ordre dans lequel les articles peuvent être affichés.
     *
     * @param  Builder $query
     * @param  string  ...$types
     * @return Builder
     */
    public function scopeVisibilities(Builder $query, string ...$types)
    {
        if ($types === ['*']) {
            $this->selection['visibilities'] = Visibility::all();
        } else {
            $this->selection['visibilities'] = Visibility::whereIn('type', $types)->get();
        }

        return $query->where(function ($subQuery) {
            return $this->prepareVisibilitiesQuery($subQuery);
        });
    }

    /**
     * Prépare la requête pour chaque visibilité.
     *
     * @param  Builder $subQuery
     * @return Builder
     */
    protected function prepareVisibilitiesQuery(Builder $subQuery)
    {
        $subQuery = $subQuery->whereNull('visibility_id');

        if (\Scopes::isClientToken(request())) {
            foreach ($this->selection['visibilities'] as $visibility) {
                $subQuery = $subQuery->orWhere('visibility_id', $visibility->id);
            }
        } else {
            $user = $this->getUserForVisibility();

            foreach ($this->selection['visibilities'] as $visibility) {
                $method = [$this, 'scope'.ucfirst($visibility->type).'Visibility'];
                if (method_exists(...$method)) {
                    // Soit la visibilité est définié par une méthode.
                    $subQuery = $subQuery->orWhere(function ($subSubQuery) use ($method) {
                        return call_user_func($method, $subSubQuery);
                    });
                } else if ($user && $user->{'is'.ucfirst($visibility->type)}()) {
                    // Soit on vérifie que le user est du type.
                    $subQuery = $subQuery->orWhere('visibility_id', $visibility->id);
                }
            }
        }

        return $subQuery;
    }

    /**
     * Défini l'utilisateur sur lequel se baser pour la visibilité.
     *
     * @param User $user
     * @return string
     */
    public static function setUserForVisibility(User $user=null)
    {
        static::$userForVisibility = $user;

        return static::class;
    }

    /**
     * Donne l'utilisateur sur lequel se baser pour la visibilité.
     *
     * @return User|null
     */
    protected function getUserForVisibility()
    {
        return (static::$userForVisibility ?? (static::$userForVisibility = \Auth::user()));
    }

    /**
     * Retourne la visibilité correspondante au type demandé.
     *
     * @param  string $type
     * @return Visibility
     */
    public function getSelectionForVisibility(string $type)
    {
        if (!isset($this->selection['visibilities']) || !is_array($this->selection['visibilities'])) {
            return Visibility::where('type', $type)->first();
        }

        foreach ($this->selection['visibilities'] as $visibility) {
            if ($visibility->type === $type) {
                return $visibility;
            }
        }

        throw new PortailException('Le type de visibilité '.$type.' n\'existe pas ou n\'est pas requis');
    }

    /**
     * Scope spécifique pour n'avoir que les ressources publiques.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePublicVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('public');

        return $query->where('visibility_id', $visibility->id);
    }

    /**
     * Scope spécifique pour n'avoir que les ressources privées.
     *
     * @param  Builder $query
     * @return Builder|null
     */
    abstract public function scopePrivateVisibility(Builder $query);

    /**
     * Scope spécifique pour n'avoir que les ressources internes.
     *
     * @param  Builder $query
     * @return Builder|null
     */
    public function scopeInternalVisibility(Builder $query)
    {
        // Accessible uniquement par les clients OAuth2.
        if (!$this->getUserForVisibility()) {
            $visibility = $this->getSelectionForVisibility('internal');

            return $query->where('visibility_id', $visibility->id);
        }
    }
}

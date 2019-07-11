<?php
/**
 * Adds a selector concerning visibilities.
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
    // We need to know the user on which apply the visibility.
    protected static $userForVisibility;

    /**
     * Specific scope concerning the display order for articles.
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
     * Prepare the request for each visibility.
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
                    // Either the visibility is defined by a method.
                    $subQuery = $subQuery->orWhere(function ($subSubQuery) use ($method) {
                        return call_user_func($method, $subSubQuery);
                    });
                } else if ($user && $user->{'is'.ucfirst($visibility->type)}()) {
                    // Or we verify that the is of the right type.
                    $subQuery = $subQuery->orWhere('visibility_id', $visibility->id);
                }
            }
        }

        return $subQuery;
    }

    /**
     * Sets the user for visibility.
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
     * Gets the user for visibility.
     *
     * @return User|null
     */
    protected function getUserForVisibility()
    {
        return (static::$userForVisibility ?? (static::$userForVisibility = \Auth::user()));
    }

    /**
     * Returns the visibility of a given type. 
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
     * Specific scope to have only public resources.
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
     * Specific scope to have only private resources.
     *
     * @param  Builder $query
     * @return Builder|null
     */
    abstract public function scopePrivateVisibility(Builder $query);

    /**
     * Specific scope to have only internal resources.
     *
     * @param  Builder $query
     * @return Builder|null
     */
    public function scopeInternalVisibility(Builder $query)
    {
        // Only OAuth2 clients can access this.
        if (!$this->getUserForVisibility()) {
            $visibility = $this->getSelectionForVisibility('internal');

            return $query->where('visibility_id', $visibility->id);
        }
    }
}

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

use App\Models\Visibility;
use App\Exceptions\PortailException;
use Illuminate\Database\Eloquent\Builder;

trait HasVisibilitySelection
{
    /**
     * Scope spécifique concernant l'ordre dans lequel les articles peuvent être affichés.
     *
     * @param  Builder $query
     * @param  string  $order
     * @return Builder
     */
    public function scopeVisibilities(Builder $query, string ...$types)
    {
        if ($types === ['*']) {
            $this->selection['visibilites'] = Visibility::all();
        } else {
            $this->selection['visibilites'] = Visibility::whereIn('type', $types)->get();
        }

        $query = $query->whereNull('visibility_id');

        foreach ($this->selection['visibilites'] as $visibility) {
            $method = [$this, 'scope'.ucfirst($visibility->type).'Visibility'];

            if (method_exists(...$method)) {  // Soit la visibilité est définié par une méthode
                $query = $query->orWhere(function ($subQuery) use ($method) {
                    return call_user_func($method, $subQuery);
                });
            } else if (\Auth::user()->{'is'.ucfirst($visibility->type)}()) {  // Soit on vérifie que le user est du type
                $query = $query->orWhere(function ($subQuery) use ($visibility) {
                    return $subQuery->where('visibility_id', $visibility->id);
                });
            }
        }

        return $query;
    }

    public function getSelectionVisibility(string $type) {
        if (!isset($this->selection['visibilites']) || !is_array($this->selection['visibilites'])) {
            return Visibility::where('type', $type)->first();
        }

        foreach ($this->selection['visibilites'] as $visibility) {
            if ($visibility->type === $type) {
                return $visibility;
            }
        }

        throw new PortailException('Le type de visibilité '.$type.' n\'existe pas ou n\'est pas requis');
    }

    /**
     * Scope spécifique pour n'avoir que les ressources privées.
     *
     * @param  Builder $query
     * @return Builder|null
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionVisibility('private');

        return $query->where('visibility_id', $visibility->id);
    }

    /**
     * Scope spécifique pour n'avoir que les ressources internes.
     *
     * @param  Builder $query
     * @return Builder|null
     */
    public function scopeInternalVisibility(Builder $query)
    {
        // Accessible uniquement par les clients OAuth2.
        if (!\Auth::id()) {
            $visibility = $this->getSelectionVisibility('internal');

            return $query->where('visibility_id', $visibility->id);
        }
    }
}

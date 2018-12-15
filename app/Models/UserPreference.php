<?php
/**
 * Modèle correspondant aux préférences utilisateurs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Traits\Model\HasKeyValue;
use Illuminate\Database\Eloquent\Builder;

class UserPreference extends Model
{
    use HasKeyValue;

    protected $table = 'users_preferences';

    protected $fillable = [
        'user_id', 'key', 'value', 'type', 'only_for',
    ];

    protected $must = [
        'key', 'value'
    ];

    /**
     * Scope pour sélectionner par instance.
     *
     * @param  Builder $query
     * @param  string  $only_for
     * @return Builder
     */
    public function scopeOnlyFor(Builder $query, string $only_for)
    {
        return $query->where('only_for', $only_for);
    }
}

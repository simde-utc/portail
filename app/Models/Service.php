<?php
/**
 * Model corresponding to services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\{
    SoftDeletes, Builder
};
use App\Traits\Model\{
    HasDeletedSelection, HasVisibilitySelection
};

class Service extends Model
{
    use SoftDeletes, HasDeletedSelection, HasVisibilitySelection;

    protected $fillable = [
        'name', 'shortname', 'login', 'image', 'description', 'url', 'visibility_id'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    protected $with = [
        'visibility',
    ];

    protected $must = [
        'name', 'shortname', 'login', 'image', 'description', 'url', 'visibility'
    ];

    protected $selection = [
        'visibilities' => '*',
        'order' => 'oldest',
        'filter' => [],
        'deleted' => 'without',
    ];

    /**
     * Specific scope to have only public ressources.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');

        // Private services are displayed only to the user with the admin permission.
        if (($user = \Auth::user()) && $user->hasOnePermission('admin')) {
            return $query->where('visibility_id', $visibility->id);
        }
    }

    /**
     * Relation with the visibility.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Relation with followers.
     *
     * @return mixed
     */
    public function followers()
    {
        return $this->hasMany(User::class, 'services_followers');
    }
}

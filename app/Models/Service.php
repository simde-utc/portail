<?php
/**
 * Modèle correspondant aux services.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Model\HasDeletedSelection;

class Service extends Model
{
    use SoftDeletes, HasDeletedSelection;

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
        'name', 'shortname', 'login', 'image', 'description', 'url'
    ];

    protected $selection = [
        'order' => 'oldest',
        'filter' => [],
        'deleted' => 'without',
    ];

    /**
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Relation avec les suiveurs.
     *
     * @return mixed
     */
    public function followers()
    {
        return $this->hasMany(User::class, 'services_followers');
    }
}

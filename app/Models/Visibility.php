<?php
/**
 * Modèle correspondant aux visibilités.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Models\Visibility;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Model\HasStages;

class Visibility extends Model
{
    use HasStages;

    protected $table = 'visibilities';

    protected $fillable = [
        'type', 'name', 'parent_id'
    ];

    protected $selection = [
        'paginate' => [],
        'order' => [],
        'filter' => [],
    ];

    /**
     * Retrouve une visibilité par son type.
     * TODO: Transformer en scope.
     * TODO: A exporter en Trait
     *
     * @param  string $type
     * @return Visibility
     */
    public static function findByType(string $type)
    {
        return static::where('type', $type)->first();
    }

    public static function public()
    {
        return static::findByType('public');
    }

    /**
     * Relation avec la visibilité parent
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(Visibility::class, 'parent_id');
    }

    /**
     * Relation avec les visibilités enfants
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Visibility::class, 'parent_id');
    }

    /**
     * Relation avec l'article
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    /**
     * Relation avec l'événement
     *
     * @return mixed
     */
    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }
}

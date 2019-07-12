<?php
/**
 * Model corresponding to aux visibilités.
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

    protected $must = [
        'type'
    ];

    protected $selection = [
        'paginate' => [],
        'order' => [],
        'filter' => [],
    ];

    /**
     * Finds a visibility from its type.
     * TODO: Transform into scope.
     * TODO: To export into a trait.
     *
     * @param  string|null $type
     * @return Visibility|null
     */
    public static function findByType(string $type=null)
    {
        return static::where('type', $type)->first();
    }

    /**
     * Returns the public visibility.
     *
     * @return mixed
     */
    public static function public()
    {
        return static::findByType('public');
    }

    /**
     * Relation with parent visibility.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(Visibility::class, 'parent_id');
    }

    /**
     * Relation with child visibilities.
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Visibility::class, 'parent_id');
    }

    /**
     * Relation with an article.
     *
     * @return mixed
     */
    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    /**
     * Relation with an event.
     *
     * @return mixed
     */
    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }
}

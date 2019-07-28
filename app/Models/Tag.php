<?php
/**
 * Model corresponding to tags.
 *
 * TODO: must, etc...
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

class Tag extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    /**
     * Relation with an article.
     *
     * @return mixed
     */
    public function article()
    {
        return $this->morphedByMany(Article::class, 'used_by', 'tags_used')->withTimestamps();
    }
}

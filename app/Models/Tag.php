<?php
/**
 * Modèle correspondant aux tags.
 *
 * TODO: Fillable, must, etc...
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * Relation avec l'article.
     *
     * @return mixed
     */
    public function article()
    {
        return $this->morphedByMany(Article::class, 'used_by', 'tags_used');
    }
}

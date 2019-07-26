<?php
/**
 * Model corresponding to article actions.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use App\Traits\Model\HasKeyValue;

class ArticleAction extends Model
{
    use HasKeyValue;

    protected $table = 'articles_actions';

    protected $fillable = [
        'article_id', 'user_id', 'key', 'value', 'type'
    ];

    protected $must = [
        'created_at',
    ];

    /**
     * Relation with articles.
     *
     * @return mixed
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}

<?php
/**
 * Modèle correspondant aux actions des articles.
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

    public $incrementing = false;

    protected $table = 'articles_actions';

    protected $primaryKey = [
        'article_id', 'user_id', 'key'
    ];

    protected $fillable = [
        'article_id', 'user_id', 'key', 'value', 'type'
    ];

    protected $must = [
        'created_at',
    ];

    protected $hidden = [
        'visibility_id',
    ];

    /**
     * Relation avec les articles.
     *
     * @return mixed
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Relation avec l'utilisateur.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

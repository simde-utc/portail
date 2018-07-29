<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    // TODO(Natan): Include user ! using $with

    use SoftDeletes;

    protected $table = 'comments';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'body', 'parent_id', 'user_id', 'visibility_id',
    ];

    public function commentable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * List Comments
     *
     * Retourne la liste des commentaires.
     * @param array $comments
     * @param int $parent_id
     * @return array
     */
    public static function getTree(array $comments, $parent_id = 0) {
        $branch = array();

        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parent_id) {
                $children = self::getTree($comments, $comment['id']);

                if ($children) {
                    $comment['children'] = $children;
                } else {
                    $comment['children'] = array();
                }
                $branch[] = $comment;
            }
        }

        return $branch;
    }
}

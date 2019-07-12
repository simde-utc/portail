<?php
/**
 * Model corresponding to comments.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Contracts\Ownership\CanBeOwner;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Traits\Model\HasDeletedSelection;
use App\Interfaces\Model\CanHaveComments;
use App\Interfaces\Model\CanComment;

class Comment extends Model implements CanBeOwner, OwnableContract, CanHaveComments
{
    use HasMorphOwner, HasCreatorSelection, SoftDeletes, HasDeletedSelection;

    protected $fillable = [
        'body', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $with = [
        'created_by', 'owned_by',
    ];

    protected $withModelName = [
        'created_by', 'owned_by',
    ];

    protected $must = [
        'body', 'created_by', 'created_at',
    ];

    /**
     * Relation with the creator.
     *
     * @return mixed
     */
    public function created_by()
    {
        return $this->morphTo('created_by');
    }

    /**
     * Relation with this comment owner.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo('owned_by');
    }

    /**
     * Relation with comments.
     *
     * @return mixed
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'owned_by');
    }

    /**
     * Indicates if the comment is accessible.
     * Only the person who owns the calendar is allowed to see it.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentAccessibleBy(string $user_id): bool
    {
        return $this->owned_by->isCommentAccessibleBy($user_id);
    }

    /**
     * Indicates if le commentaire is manageable.
     * Only the person who owns the calendar is allowed to modify it.
     *
     * @param  CanComment $model
     * @return boolean
     */
    public function isCommentManageableBy(CanComment $model): bool
    {
        return $this->owned_by->isCommentManageableBy($model);
    }
}

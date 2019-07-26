<?php
/**
 * Model corresponding to articles.
 *
 * @author Thomas Meurou <thomas.meurou@yahoo.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natan.danous@gmail.com>
 * @author Romain Maliach <r.maliach@live.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Contracts\Ownership\CanBeOwner;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\{
    HasCreatorSelection, HasOwnerSelection, HasVisibilitySelection
};
use App\Models\ArticleAction;
use App\Interfaces\Model\CanHaveComments;
use App\Interfaces\Model\CanComment;

class Article extends Model implements CanBeOwner, OwnableContract, CanHaveComments
{
    use HasMorphOwner, HasCreatorSelection, HasOwnerSelection, HasVisibilitySelection;

    protected $fillable = [
        'title', 'description', 'content', 'image', 'event_id', 'visibility_id',
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
    ];

    protected $with = [
        'created_by', 'owned_by', 'tags', 'visibility', 'event',
    ];

    protected $withModelName = [
        'created_by', 'owned_by',
    ];

    protected $must = [
        'title', 'description', 'content', 'image', 'owned_by', 'created_at', 'event', 'visibility'
    ];

    protected $hidden = [
        'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type', 'visibility_id', 'event_id',
    ];

    protected $selection = [
        'visibilities' => '*',
        'paginate' 	=> 10,
        'order'		=> [
            'default' 	=> 'latest',
            'columns'	=> [
                'name' 	=> 'title',
            ],
        ],
        'month'		=> [],
        'week'		=> [],
        'day'		=> [],
        'interval'	=> [],
        'date'		=> [],
        'dates'		=> [],
        'creator' 	=> [],
        'owner' 	=> [],
        'action'	=> [],
        'filter'	=> [],
    ];

    /**
     * Specific scope concerning the order to display articles.
     *
     * @param  Builder $query
     * @param  string  $order
     * @return Builder
     */
    public function scopeOrder(Builder $query, string $order)
    {
        if ($order === 'liked' || $order === 'disliked') {
            $actionTable = (new ArticleAction)->getTable();

            $query = $query->where($actionTable.'.key', 'LIKED')
            ->join($actionTable, $actionTable.'.article_id', '=', $this->getTable().'.id')
            ->groupBy($this->getTable().'.id')
            ->orderByRaw('SUM(IF('.$actionTable.'.value='.((string) true).', 10, -5)) '.($order === 'liked' ? 'desc' : 'asc'));

            $query->selectRaw($this->getTable().'.*');
        } else {
            return parent::scopeOrder($query, $order);
        }

        return $query;
    }

    /**
     * Specific scope to display.
     *
     * @param  Builder $query
     * @param  string  $action
     * @return Builder
     */
    public function scopeAction(Builder $query, string $action)
    {
        $actionTable = (new ArticleAction)->getTable();

        if (substr($action, 0, 1) === '!') {
            $action = substr($action, 1);

            $query->whereNotExists(function ($query) use ($action, $actionTable) {
                if (\Auth::id()) {
                    $query = $query->where($actionTable.'.user_id', \Auth::id());
                }

                return $query->selectRaw('NULL')
                     ->from($actionTable)
                     ->where($actionTable.'.key', strtoupper($action))
                     ->whereRaw($actionTable.'.article_id = '.$this->getTable().'.id');
            });
        } else if (substr($action, 0, 2) === 'un' || substr($action, 0, 3) === 'dis') {
            $action = substr($action, 0, 2) === 'un' ? substr($action, 2) : substr($action, 3);

            $query = $query->where($actionTable.'.key', strtoupper($action))
                ->where($actionTable.'.value', '<', 1)
                ->join($actionTable, $actionTable.'.article_id', '=', $this->getTable().'.id');
        } else {
            $query = $query->where($actionTable.'.key', strtoupper($action))
                ->where($actionTable.'.value', '>', 0)
                ->join($actionTable, $actionTable.'.article_id', '=', $this->getTable().'.id');
        }

        if (\Auth::id()) {
            $query = $query->where($actionTable.'.user_id', \Auth::id());
        }

        return $query;
    }

    /**
     * Specific scope to have only the private resources.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');
        $user = $this->getUserForVisibility();

        if ($user) {
            $asso_ids = $user->currentJoinedAssos()->pluck('id')->toArray();

            return $query->where('visibility_id', $visibility->id)->where(function ($subQuery) use ($user, $asso_ids) {
                return $subQuery->where(function ($subSubQuery) use ($user) {
                    return $subSubQuery->where('owned_by_type', User::class)->where('owned_by_id', $user->id);
                })->orWhere(function ($subSubQuery) use ($asso_ids) {
                    return $subSubQuery->where('owned_by_type', Asso::class)->whereIn('owned_by_id', $asso_ids);
                })->orWhere(function ($subSubQuery) use ($asso_ids) {
                    return $subSubQuery->where('owned_by_type', Client::class)
                        ->whereIn('owned_by_id', Client::whereIn('asso_id', $asso_ids)->pluck('id')->toArray());
                })->orWhere(function ($subSubQuery) use ($user) {
                    return $subSubQuery->where('owned_by_type', Group::class)
                        ->whereIn('owned_by_id', $user->groups()->pluck('id')->toArray());
                });
            });
        }
    }

    /**
     * Generate on the fly the "description" attribute.
     *
     * @return string	Rescription field or a shortcut of the content.
     */
    public function getDescriptionAttribute()
    {
        $description = $this->getOriginal('description');

        if ($description) {
            return $description;
        } else {
            return trimText($this->getAttribute('content'), validation_max('string'));
        }
    }

    /**
     * Relation with tags.
     *
     * @return mixed
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'used_by', 'tags_used')->withTimestamps();
    }

    /**
     * Relation with the visibility.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class, 'visibility_id');
    }

    /**
     * Relation with the event.
     *
     * @return mixed
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relation with the actions.
     *
     * @return mixed
     */
    public function actions()
    {
        return $this->hasMany(ArticleAction::class);
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
     * Return if the current user can see this article comments.
     *
     * @param  string $user_id
     * @return boolean
     */
    public function isCommentAccessibleBy(string $user_id): bool
    {
        return User::find($user_id)->{"is".ucfirst($this->visibility->type)}();
    }

    /**
     * Return si un modèle peut modifier les commentaires de l'article.
     * A comment is only writable by the article owner instance or a user.
     *
     * @param  CanComment $model
     * @return boolean
     */
    public function isCommentManageableBy(CanComment $model): bool
    {
        if ($model instanceof User) {
            return true;
        } else {
            return get_class($model) === $this->owned_by_type && $model->id === $this->owned_by_id;
        }
    }
}

<?php
/**
 * Model corresponding to FAQ categories.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use App\Traits\Model\{
    HasStages, HasLang, HasVisibilitySelection
};

class FaqCategory extends Model
{
    use HasStages, HasLang, HasVisibilitySelection;

    protected $table = 'faqs_categories';

    protected $fillable = [
        'name', 'description', 'lang', 'parent_id', 'visibility_id',
    ];

    protected $hidden = [
        'parent_id', 'visibility_id',
    ];

    protected $with = [
        'parent', 'visibility', 'questions',
    ];

    protected $optional = [
        'children', 'parent',
    ];

    protected $must = [
        'name', 'description', 'visibility',
    ];

    // Children in case of staged mode display.
    protected $selection = [
        'visibilities' => '*',
        'lang' => '~',
        'filter' => [],
        'stage' => null,
        'stages' => null,
    ];

    /**
     * Relation with the parent.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(FaqCategory::class, 'parent_id');
    }

    /**
     * Relation with the children.
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(FaqCategory::class, 'parent_id');
    }

    /**
     * Relation with the visibility.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Relation with the questions.
     *
     * @return mixed
     */
    public function questions()
    {
        return $this->hasMany(Faq::class, 'category_id');
    }

    /**
     * Specifique scope to get private resources.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');

        // Private FAQs are displayed only to 'faq-question' permissions owners.
        if (($user = \Auth::user()) && $user->hasOnePermission('faq-question')) {
            return $query->where('visibility_id', $visibility->id);
        }
    }
}

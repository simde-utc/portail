<?php
/**
 * Model corresponding to FAQs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use App\Traits\Model\HasVisibilitySelection;

class Faq extends Model
{
    use HasVisibilitySelection;

    protected $fillable = [
        'question', 'answer', 'category_id', 'visibility_id',
    ];

    protected $hidden = [
        'category_id', 'visibility_id',
    ];

    protected $with = [
        'visibility'
    ];

    protected $must = [
        'question', 'answer', 'visibility',
    ];

    protected $selection = [
        'visibilities' => '*',
    ];

    /**
     * Relation with the category.
     *
     * @return mixed
     */
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
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
     * Specific scope to have the private resources.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');

        // Privates faqs are displayed only to 'faq' permission owners.
        if (($user = \Auth::user()) && $user->hasOnePermission('faq')) {
            return $query->where('visibility_id', $visibility->id);
        }
    }
}

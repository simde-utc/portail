<?php
/**
 * Modèle correspondant aux FAQs.
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
     * Relation avec la catégorie.
     *
     * @return mixed
     */
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    /**
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Scope spécifique pour avoir les ressources privées.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');

        // Les faqs privés sont affiché uniquement aux personnes ayant la permission 'faq'.
        if (($user = \Auth::user()) && $user->hasOnePermission('faq')) {
            return $query->where('visibility_id', $visibility->id);
        }
    }
}

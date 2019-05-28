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

use App\Traits\Model\{
    HasStages, HasLang
};

class FaqCategory extends Model
{
    use HasStages, HasLang;

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
        'name', 'description', 'lang', 'visibility',
    ];

    // Children dans le cas où on affiche en mode étagé.
    protected $selection = [
        'visibilities' => '*',
        'lang' => '*',
        'filter' => [],
        'stage' => null,
        'stages' => null,
    ];

    /**
     * Relation avec le parent.
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(FaqCategory::class, 'parent_id');
    }

    /**
     * Relation avec les enfants.
     *
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(FaqCategory::class, 'parent_id');
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
     * Relation avec la questions.
     *
     * @return mixed
     */
    public function questions()
    {
        return $this->hasMany(Faq::class, 'category_id');
    }
}

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

class Faq extends Model
{
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
}

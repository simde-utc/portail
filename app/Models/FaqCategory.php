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

use App\Traits\Model\HasStages;

class FaqCategory extends Model
{
    use HasStages;

    protected $table = 'faqs_categories';

    protected $fillable = [
        'name', 'description', 'parent_id', 'visibility_id',
    ];

    protected $hidden = [
        'parent_id', 'visibility_id',
    ];

    protected $with = [
        'parent', 'visibility'
    ];

    protected $must = [
        'name', 'description', 'category',
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
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }
}

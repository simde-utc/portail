<?php
/**
 * Ajoute au controlleur un accès aux FAQs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits\Controller\v1;

use App\Models\{
    Faq, FaqCategory
};
use Illuminate\Http\Request;
use App\Exceptions\PortailException;

trait HasFaqs
{
    /**
     * Récupère une catégorie FAQ par son id si elle existe.
     *
     * @param Request  $request
     * @param string   $category_id
     * @return FaqCategory
     */
    protected function getFaqCategory(Request $request, string $category_id): FaqCategory
    {
        $category = FaqCategory::with('parent')->find($category_id);

        if ($category) {
            return $category->makeHidden('parent_id');
        } else {
            abort(404, "Catégorie FAQ non trouvée");
        }
    }
}

<?php
/**
 * Adds the controller an access to FAQs.
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
     * Retrieves a FAQ category with its ID if it exists.
     *
     * @param Request $request
     * @param string  $category_id
     * @return FaqCategory
     */
    protected function getFaqCategory(Request $request, string $category_id): FaqCategory
    {
        $category = FaqCategory::with(['parent', 'children'])->find($category_id);

        if ($category) {
            return $category->makeHidden('parent_id');
        } else {
            abort(404, "Catégorie FAQ non trouvée");
        }
    }

    /**
     * Retrieves a FAQ with its ID if it exists.
     *
     * @param Request $request
     * @param string  $category_id
     * @param string  $faq_id
     * @return Faq
     */
    protected function getFaq(Request $request, string $category_id, string $faq_id): Faq
    {
        $faq = Faq::where('category_id', $this->getFaqCategory($request, $category_id)->id)->find($faq_id);

        if ($faq) {
            return $faq;
        } else {
            abort(404, "Question FAQ non trouvée");
        }
    }
}

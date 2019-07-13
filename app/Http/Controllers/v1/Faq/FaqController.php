<?php
/**
 * Manages FAQs.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Faq;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\FaqRequest;
use App\Models\{
    Faq, FaqCategory
};
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasFaqs;

class FaqController extends Controller
{
    use HasFaqs;

    /**
     * Must be able to manage FAQs.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::allowPublic()->matchOne('user-get-faqs-questions', 'client-get-faqs-questions'),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
            \Scopes::matchOne('user-create-faqs-questions', 'client-create-faqs-questions'),
            ['only' => ['create']]
        );
        $this->middleware(
            \Scopes::matchOne('user-edit-faqs-questions', 'client-edit-faqs-questions'),
            ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOne('user-remove-faqs-questions', 'client-remove-faqs-questions'),
            ['only' => ['remove']]
        );
    }

    /**
     * Lists FAQs.
     *
     * @param Request $request
     * @param string  $category_id
     * @return JsonResponse
     */
    public function index(Request $request, string $category_id): JsonResponse
    {
        $faqs = Faq::where('category_id', $this->getFaqCategory($request, $category_id)->id)
            ->getSelection()->map(function ($faq) {
                return $faq->hideData();
            });

        return response()->json($faqs, 200);
    }

    /**
     * Adds a FAQ.
     *
     * @param FaqRequest $request
     * @param string     $category_id
     * @return JsonResponse
     */
    public function store(FaqRequest $request, string $category_id): JsonResponse
    {
        $faq = Faq::create(array_merge($request->input(), [
            'category_id' => $category_id,
        ]));

        return response()->json($faq, 201);
    }

    /**
     * Shows a FAQ.
     *
     * @param Request $request
     * @param string  $category_id
     * @param string  $faq_id
     * @return JsonResponse
     */
    public function show(Request $request, string $category_id, string $faq_id): JsonResponse
    {
        $faq = $this->getFaq($request, $category_id, $faq_id);

        return response()->json($faq->hideSubData(), 200);
    }

    /**
     * Updates a FAQ.
     *
     * @param FaqRequest $request
     * @param string     $category_id
     * @param string     $faq_id
     * @return JsonResponse
     */
    public function update(FaqRequest $request, string $category_id, string $faq_id): JsonResponse
    {
        $faq = $this->getFaq($request, $category_id, $faq_id);

        if ($faq->update($request->input())) {
            return response()->json($faq, 200);
        } else {
            abort(500, 'La question FAQ n\'a pas pu être modifiée');
        }
    }

    /**
     * Deletes a FAQ.
     *
     * @param Request $request
     * @param string  $category_id
     * @param string  $faq_id
     * @return void
     */
    public function destroy(Request $request, string $category_id, string $faq_id): void
    {
        $faq = $this->getFaq($request, $category_id, $faq_id);
        $faq->delete();

        abort(204);
    }
}

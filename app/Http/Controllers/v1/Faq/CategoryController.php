<?php
/**
 * Manages FAQs' categories.
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
use App\Models\FaqCategory;
use App\Http\Requests\FaqCategoryRequest;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasFaqs;

class CategoryController extends Controller
{
    use HasFaqs;

    /**
     * Must be able to manage FAQ's questions.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::allowPublic()->matchOne('user-get-faqs-categories', 'client-get-faqs-categories'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOne('user-create-faqs-categories', 'client-create-faqs-categories'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOne('user-edit-faqs-categories', 'client-edit-faqs-categories'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOne('user-remove-faqs-categories', 'client-remove-faqs-categories'),
            ['only' => ['destroy']]
        );
    }

    /**
     * Lists categories.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $categories = FaqCategory::with('parent')->getSelection()->map(function ($category) {
            return $category->hideData();
        });

        return response()->json($categories, 200);
    }

    /**
     * Adds a FAQ category.
     *
     * @param FaqCategoryRequest $request
     * @return JsonResponse
     */
    public function store(FaqCategoryRequest $request): JsonResponse
    {
        $category = FaqCategory::create($request->input());

        return response()->json($category, 201);
    }

    /**
     * Shows a FAQ category.
     *
     * @param Request $request
     * @param string  $category_id
     * @return JsonResponse
     */
    public function show(Request $request, string $category_id): JsonResponse
    {
        $category = $this->getFaqCategory($request, $category_id);

        return response()->json($category->hideSubData(), 200);
    }

    /**
     * Updates a FAQ category.
     *
     * @param FaqCategoryRequest $request
     * @param string             $category_id
     * @return JsonResponse
     */
    public function update(FaqCategoryRequest $request, string $category_id): JsonResponse
    {
        $category = $this->getFaqCategory($request, $category_id);

        if ($category->update($request->input())) {
            return response()->json($category, 200);
        } else {
            abort(500, 'La catégorie FAQ n\'a pas pu être modifiée');
        }
    }

    /**
     * Deletes a FAQ category.
     *
     * @param Request $request
     * @param string  $category_id
     * @return void
     */
    public function destroy(Request $request, string $category_id): void
    {
        $category = $this->getFaqCategory($request, $category_id);

        if ($category->children()->exists()) {
            abort(400, 'Il n\'est pas possible de Deletesr une catégorie FAQ parente');
        }

        $category->delete();

        abort(204);
    }
}

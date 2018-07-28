<?php

namespace App\Http\Controllers\v1\Comment;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;

/**
 * @resource Comment
 *
 * Les commentaires écrits par les utilisateurs
 */
class CommentController extends Controller
{
    /* TODO(Natan): - scopes  config/ + middleware
                    - store
                    - show
                    - update
                    - destroy
    */

    /**
     * Scopes Commentaire
     *
     * Les Scopes requis pour manipuler les Commentaires
     */
    public function __construct() {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-articles', 'client-get-articles'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-articles', 'client-set-articles'),
            ['only' => ['store', 'update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-articles', 'client-manage-articles'),
            ['only' => ['destroy']]
        );
    }


    /**
     * List Comments
     *
     * Retourne la liste des commentaires.
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function index(CommentRequest $request): JsonResponse {
        $comments = Comment::getTree($request->resource->comments->toArray());

        return response()->json($comments, 200);
    }

    /**
     * Create Comment
     *
     * Créer un commentaire.
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function store(CommentRequest $request): JsonResponse {        
        $comment = $request->resource->comments()->create([
            'body' => $request->input('body'),
            'parent_id' => $request->input('parent_id'),
            'user_id' => \Auth::user()->id,
            'visibility_id' => $request->input('visibility_id'),
        ]);

        if ($comment)
            return response()->json($comment, 201);
        else
            return response()->json(['message' => 'Impossible d\'enregistrer le commentaire'], 500); 
    }

    /**
     * Show Comment
     *
     * Affiche le commentaire.
     * @param CommentRequest $request
     * @param  int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse {
        
    }

    /**
     * Update Comment
     *
     * Met à jour le commentaire.
     * @param CommentRequest $request
     * @param  int $id
     * @return JsonResponse
     */
    public function update(ArticleRequest $request, int $id): JsonResponse {
        
    }

    /**
     * Delete Comment
     *
     * Supprime le commentaire.
     * @param ArticleRequest $request
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(ArticleRequest $request, $id): JsonResponse {
        // Attention aux children ...
    }
}

<?php

namespace App\Http\Controllers\v1\Comment;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Carbon\Carbon;

/**
 * @resource Comment
 *
 * Les commentaires écrits par les utilisateurs
 */
class CommentController extends Controller
{
    /* TODO(Natan): - ajout du truc de @Samy (stages) pour choper le tree
                    - destroy
    */

    /**
     * Scopes Commentaire
     *
     * Les Scopes requis pour manipuler les Commentaires
     */
    public function __construct() {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-comments', 'client-get-comments'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-set-comments', 'client-set-comments'),
            ['only' => ['store', 'update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-comments', 'client-manage-comments'),
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
        $comments = Comment::getTree($request->resource
                                        ->comments()
                                        ->get()
                                        ->toArray());

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
        $parent_id = $request->input('parent_id');

        if ($request->resource->comments()->find($request->input('parent_id')) == null)
            $parent_id = null;

        $comment = $request->resource->comments()->create([
            'body' => $request->input('body'),
            'parent_id' => $parent_id,
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
     * @return JsonResponse
     */
    public function show(CommentRequest $request): JsonResponse {  
        $comment = $request->resource->comments()->find($request->comment);

        if ($comment && ($comment->deleted_at == null))
            return response()->json($comment, 200);
        else
            return response()->json(['message' => 'Impossible de trouver le commentaire'], 404);
    }

    /**
     * Update Comment
     *
     * Met à jour le commentaire.
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function update(CommentRequest $request): JsonResponse {
        $comment = $request->resource->comments()->find($request->comment);

        if (!$comment || ($comment->deleted_at != null))
            return response()->json(['message' => 'Impossible de trouver le commentaire'], 404);

        $parent_id = $request->input('parent_id');

        if ($request->resource->comments()->find($request->input('parent_id')) == null)
            $parent_id = null;

        $comment->update([
            'body' => $request->input('body'),
            'parent_id' => $parent_id,
            'user_id' => \Auth::user()->id,
            'visibility_id' => $request->input('visibility_id'),
        ]);

        if ($comment)
            return response()->json($comment, 201);
        else
            return response()->json(['message' => 'Impossible de modifier le commentaire'], 500);
    }

    /**
     * Delete Comment
     *
     * Supprime le commentaire.
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function destroy(CommentRequest $request): JsonResponse {
        $comment = $request->resource->comments()->find($request->comment);

        if ($comment)  {
            $comment->deleted_at = Carbon::now()->toDateTimeString();
            $comment->save();

            abort(204);
        }
        else
            abort(404, 'Impossible de trouver le commentaire');
    }
}

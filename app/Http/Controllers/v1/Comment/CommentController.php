<?php
/**
 * Manages a resource comments.
 *
 * @author Natan Danous <natan.danous@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Romain Maliach <r.maliach@live.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Comment;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Carbon\Carbon;
use App\Traits\Controller\v1\HasComments;

class CommentController extends Controller
{
    use HasComments;

    /**
     * Must be able to manage comments.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-comments', 'client-get-comments'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-comments', 'client-create-comments'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-comments', 'client-edit-comments'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-remove-comments', 'client-remove-comments'),
            ['only' => ['destroy']]
        );
    }

    /**
     * Lists a resource comments.
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function index(CommentRequest $request): JsonResponse
    {
        $this->checkTokenRights($request);

        if (\Auth::id() && !$request->resource->isCommentAccessibleBy(\Auth::id())) {
            abort(503, 'Vous n\'avez pas le droit de voir ces commentaires');
        }

        $comments = $request->resource->comments()->getSelection()->map(function ($comment) {
            return $comment->hideData();
        });

        return response()->json($comments, 200);
    }

    /**
     * Creates a comment for a ressource.
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function store(CommentRequest $request): JsonResponse
    {
        $creater = \ModelResolver::getModel($request->input('created_by_type'))->find($request->input('created_by_id'));

        if (!$request->resource->isCommentManageableBy($creater)
            || (\Auth::id() && !$creater->isCommentWritableBy(\Auth::id()))) {
            abort(403, 'Il ne vous est pas autorisé de créer un commentaire pour cette instance');
        }

        $comment = $request->resource->comments()->create([
            'body' => $request->input('body'),
            'created_by_type' => get_class($creater),
            'created_by_id' => $creater->id,
        ]);

        $comment->changeOwnerTo($request->resource)->save();

        return response()->json($comment->hideSubData(), 201);
    }

    /**
     * Shows a resource's comment.
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function show(CommentRequest $request): JsonResponse
    {
        $comment = $this->getComment($request);

        return response()->json($comment->hideSubData(), 200);
    }

    /**
     * Updates a resource's comment.
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function update(CommentRequest $request): JsonResponse
    {
        $comment = $this->getComment($request, 'edit');

        $comment->update([
            'body' => $request->input('body'),
        ]);

        if ($comment) {
            return response()->json($comment->hideSubData(), 201);
        } else {
            return response()->json(['message' => 'Impossible de modifier le commentaire'], 500);
        }
    }

    /**
     * Deletes a resource's comment.
     *
     * @param CommentRequest $request
     * @return void
     */
    public function destroy(CommentRequest $request): void
    {
        $comment = $this->getComment($request, 'remove');

        $comment->delete();

        abort(204);
    }
}

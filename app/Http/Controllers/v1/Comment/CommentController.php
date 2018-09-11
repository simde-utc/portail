<?php

namespace App\Http\Controllers\v1\Comment;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Carbon\Carbon;
use App\Traits\Controller\v1\HasComments;

/**
 * @resource Comment
 *
 * Les commentaires écrits par les utilisateurs
 */
class CommentController extends Controller
{
  use HasComments;

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
   * List Comments
   *
   * Retourne la liste des commentaires.
   * @param \Illuminate\Http\Request $request
   * @return JsonResponse
   */
  public function index(CommentRequest $request): JsonResponse {
		$this->checkTokenRights($request);

    if (!$request->resource->isCommentAccessibleBy($user_id))
      abort(503, 'Vous n\'avez pas le droit de voir ces commentaires');

		$comments = $request->resource->comments()->getSelection()->map(function ($comment) {
			return $comment->hideData();
		});

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
    $creater = \ModelResolver::getModel($request->input($type.'_by_type'))->find($request->input($type.'_by_id'));

    if (!$request->ressource->isCommentManageableBy($creater) || (\Auth::id() && !$request->ressource->isCommentWritableBy($creater)))
      abort(403, 'Il ne vous est pas autorisé de créer un commentaire pour cette instance');

    $comment = $request->resource->comments()->create([
      'body' => $request->input('body'),
      'created_by_type' => $request->input('created_by_type'),
      'created_by_id' => $request->input('created_by_id'),
    ]);

    $comment->changeOwnerTo($request->ressource)->save();

    return response()->json($comment->hideSubData(), 201);
  }

  /**
   * Show Comment
   *
   * Affiche le commentaire.
   * @param CommentRequest $request
   * @return JsonResponse
   */
  public function show(CommentRequest $request): JsonResponse {
    $comment = $this->getComment($request);

    return response()->json($comment->hideSubData(), 200);
  }

  /**
   * Update Comment
   *
   * Met à jour le commentaire.
   * @param CommentRequest $request
   * @return JsonResponse
   */
  public function update(CommentRequest $request): JsonResponse {
    $comment = $this->getComment($request, 'edit');

    $comment->update([
      'body' => $request->input('body'),
    ]);

    if ($comment)
      return response()->json($comment->hideSubData(), 201);
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
    $comment = $this->getComment($request, 'remove');

    $comment->softDelete();
    abort(204);
  }
}

<?php
/**
 * Manages articles.
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Article;

use App\Http\Controllers\v1\Controller;
use App\Models\Model;
use App\Models\User;
use App\Models\Asso;
use App\Models\Visibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Tag;
use App\Interfaces\Model\CanHaveArticles;
use App\Traits\Controller\v1\HasArticles;
use App\Traits\Controller\v1\HasImages;

class ArticleController extends Controller
{
    use HasArticles, HasImages;

    /**
     * Must be able to manage articles.
     * Read access is public.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::allowPublic()->matchOneOfDeepestChildren(
                ['user-get-articles-assos', 'user-get-articles-groups'],
                ['client-get-articles-assos', 'client-get-articles-groups']
            ),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren(
                ['user-create-articles-assos', 'user-create-articles-groups'],
                ['client-create-articles-assos', 'client-create-articles-groups']
            ),
	        ['only' => ['create']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren(
                ['user-edit-articles-assos', 'user-edit-articles-groups'],
                ['client-edit-articles-assos', 'client-edit-articles-groups']
            ),
	        ['only' => ['edit']]
        );
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren(
                ['user-manage-articles-assos', 'user-manage-articles-groups'],
                ['client-manage-articles-assos', 'client-manage-articles-groups']
            ),
	        ['only' => ['remove']]
        );
    }

    /**
     * Retrieves the creator or the owner.
     *
     * @param  Request $request
     * @param  string  $verb
     * @param  string  $type
     * @return Model
     */
    public function getCreaterOrOwner(Request $request, string $verb='create', string $type='created')
    {
        $scopeHead = \Scopes::isUserToken($request) ? 'user' : 'client';
        $scope = $scopeHead.'-'.$verb.'-articles-'.$request->input($type.'_by_type', $scopeHead).'s-'.$type;

        if ($type === 'owned') {
            $scope = array_keys(\Scopes::getRelatives(
                $scopeHead.'-'.$verb.'-articles-'.$request->input($type.'_by_type').'s-'.$type
            ));

            if (count($scope) === 0) {
                abort(403, 'Non autorisé a possédé un article');
            }
        }

        if (!\Scopes::hasOne($request, $scope)) {
            abort(403, 'Il ne vous est pas autorisé de créer d\'articles');
        }

        if ($request->filled($type.'_by_type')) {
            if ($request->filled($type.'_by_id')) {
                $createrOrOwner = \ModelResolver::findModel(
                    $request->input($type.'_by_type'),
                    $request->input($type.'_by_id')
                );

                if (\Auth::id() && !$createrOrOwner->isArticleManageableBy(\Auth::id())) {
                    abort(403, 'L\'utilisateur n\'a pas les droits de création');
                }
            } else if ($request->input($type.'_by_type', 'client') === 'client') {
                $createrOrOwner = \Scopes::getClient($request);
            } else if ($request->input($type.'_by_type', 'client') === 'asso') {
                $createrOrOwner = \Scopes::getClient($request)->asso;
            }
        }

        if (!isset($createrOrOwner)) {
            $createrOrOwner = \Scopes::isClientToken($request) ? \Scopes::getClient($request) : \Auth::user();
        }

        if (!($createrOrOwner instanceof CanHaveArticles)) {
            abort(400, 'La personne créatrice/possédeur doit au moins pouvoir avoir un article');
        }

        return $createrOrOwner;
    }

    /**
     * Lists articles.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $articles = Article::getSelection();

        if (\Scopes::isOauthRequest($request)) {
            $articles = $articles->filter(function ($article) use ($request) {
                return $this->tokenCanSee($request, $article, 'get');
            });
        }

        return response()->json($articles->values()->map(function ($article) {
            return $article->hideData();
        }));
    }

    /**
     * Creates a article.
     *
     * @param ArticleRequest $request
     * @return JsonResponse
     */
    public function store(ArticleRequest $request): JsonResponse
    {
        $inputs = $request->all();

        // Creator or owner retrievement.
        $owner = $this->getCreaterOrOwner($request, 'create', 'owned');
        $ownerName = \ModelResolver::getNameFromObject($owner);

        // The creator can be multiple: the user, the association or the current client. Or any other.
        if ($request->input('created_by_type', 'user') === 'user'
	        && \Auth::id()
	        && $request->input('created_by_id', \Auth::id()) === \Auth::id()
	        && \Scopes::hasOne($request,
                \Scopes::getTokenType($request).'-create-articles-'.$ownerName.'s-owned-user')) {
            $creater = \Auth::user();
        } else if ($request->input('created_by_type', 'client') === 'client'
	        && $request->input('created_by_id', \Scopes::getClient($request)->id) === \Scopes::getClient($request)->id
	        && \Scopes::hasOne($request,
                \Scopes::getTokenType($request).'-create-articles-'.$ownerName.'s-owned-client')) {
            $creater = \Scopes::getClient($request);
        } else if ($request->input('created_by_type') === 'asso'
	        && $request->input('created_by_id', ($asso_id = \Scopes::getClient($request)->asso->id)) === $asso_id
	        && \Scopes::hasOne($request,
                \Scopes::getTokenType($request).'-create-articles-'.$ownerName.'s-owned-asso')) {
            $creater = \Scopes::getClient($request)->asso;
        } else {
            $creater = $this->getCreaterOrOwner($request, 'create', 'created');
        }

        $inputs['created_by_id'] = $creater->id;
        $inputs['created_by_type'] = get_class($creater);
        $inputs['owned_by_id'] = $owner->id;
        $inputs['owned_by_type'] = get_class($owner);

        if ($request->filled('event_id')) {
            // Checks if the person has the rights on the event.
            $this->getEvent($request, \Auth::user(), $inputs['event_id']);
        }

        $article = Article::create($inputs);

        // Affecting image if everything went well.
        $this->setImage($request, $article, 'articles', $article->id);

        // Tags.
        if ($request->has('tags') && is_array($inputs['tags'])) {
            $tags = Tag::all();

            foreach ($inputs['tags'] as $tag_arr) {
                if ($tag = $tags->where('name', $tag_arr['name'])->first()) {
                    $article->tags()->save($tag);
                } else {
                    $tag = new Tag;
                    $tag->name = $tag_arr['name'];
                    $tag->description = array_key_exists("description", $tag_arr) ? $tag_arr['description'] : null;
                    $tag->save();
                    $article->tags()->save($tag);
                }
            }
        }

        $article = $this->getArticle($request, \Auth::user(), $article->id);

        return response()->json($article->hideSubData(), 201);
    }

    /**
     * Shows an article.
     *
     * @param Request $request
     * @param string  $article_id
     * @return JsonResponse
     */
    public function show(Request $request, string $article_id): JsonResponse
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id);

        return response()->json($article->hideSubData());
    }

    /**
     * Updates an article.
     *
     * @param Request $request
     * @param string  $article_id
     * @return JsonResponse
     */
    public function update(Request $request, string $article_id): JsonResponse
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id, 'edit');
        $inputs = $request->all();

        if ($request->filled('owned_by_type')) {
            $owner = $this->getCreaterOrOwner($request, 'edit', 'owned');

            $inputs['owned_by_id'] = $owner->id;
            $inputs['owned_by_type'] = get_class($owner);
        }

        if ($request->filled('event_id')) {
            // Checks if the person has the rights on the event.
            $this->getEvent($request, \Auth::user(), $inputs['event_id']);
        }

        if ($article->update($inputs)) {
            // Affecting image if everything went well.
            $this->setImage($request, $article, 'articles', $article->id);

            // Tags.
            if ($request->has('tags') && is_array($inputs['tags'])) {
                $tags = Tag::all();

                foreach ($inputs['tags'] as $tag_arr) {
                    if (!$tags->where('name', $tag_arr['name'])->first()) {
                        $tag = new Tag;
                        $tag->name = $tag_arr['name'];
                        $tag->description = array_key_exists("description", $tag_arr) ? $tag_arr['description'] : null;
                        $tag->save();
                        $article->tags()->save($tag);
                    } else {
                        $tag = Tag::where('name', $tag_arr['name'])->first();
                        $article->tags()->save($tag);
                    }
                }
            }

            $article = $this->getArticle($request, \Auth::user(), $article->id);

            return response()->json($article->hideData());
        } else {
            abort(500, 'Impossible de modifier l\'article');
        }
    }

    /**
     * Deletes an article.
     *
     * @param Request $request
     * @param string  $article_id
     * @return void
     */
    public function destroy(Request $request, string $article_id): void
    {
        $article = $this->getArticle($request, \Auth::user(), $article_id, 'remove');
        $article->tags()->delete();

        if ($article->delete()) {
            $this->deleteImage('articles/'.$article->id);

            abort(204);
        } else {
            abort(500, 'L\'article n\'a pas pu être supprimé');
        }
    }
}

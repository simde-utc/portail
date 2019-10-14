# HasArticles

This trait allows a controller to access articles depending on their visibility and the user membership/types.

## getArticle method

### Parameters
- Illuminate\Http\Request : Request
- App\Models\User : User (default null)
- String : Article id
- String : Verb (default "get")

**Request**: See Laravel HTTP requests : [https://laravel.com/docs/5.8/requests](https://laravel.com/docs/5.8/requests)

**User**: An instance of the User Model. See [portail/dev/models/User.md](User Model documentation) for more information.

**Article Id**: Explicit.

**Verb**: You can add a verb from the [portail/dev/backend/oauth.md#verb-definition](verb list) check if the autenticated user is authorized to update/delete/get the given article.
 
### Return value

This method returns the requested article or abort current HTTP request with a 404 (Article not found) or a 403 (Unauthorized) error code.

### Usage example
In `app/Http/Controllers/v1/Article/ActionController.php`
```php
/**
 * List the article's actions.
 *
 * @param Request $request
 * @param string  $article_id
 * @return JsonResponse
 */
public function index(Request $request, string $article_id): JsonResponse
{
    $article = $this->getArticle($request, \Auth::user(), $article_id);
    $actions = $article->actions()->groupToArray();

    return response()->json($actions, 200);
}
```
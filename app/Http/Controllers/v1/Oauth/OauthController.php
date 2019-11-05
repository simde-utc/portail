<?php
/**
 * Allow communication with Scopes services through the API.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Oauth;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OauthController extends Controller
{
    /**
     * Get the given scopes descriptions by category.
     * 
     * @return JsonResponse
     */
    public function getByCategories(Request $request): JsonResponse
    {
        $scopes = explode(' ', $request->input('scopes'));
        $scopes = \Scopes::getByCategories($scopes);
        return response()->json($scopes);
    }
}

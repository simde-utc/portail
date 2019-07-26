<?php
/**
 * Manage accesses.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Access;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Access;

class AccessController extends Controller
{
    /**
     * Public retrievement or subscopes.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne('user-get-access', 'client-get-access')
        );
    }

    /**
     * List accesses.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $accessList = Access::getSelection()->map(function ($access) {
            return $access->hideData();
        });

        return response()->json($accessList, 200);
    }

    /**
     * Creation not possible.
     *
     * @return void
     */
    public function store(): void
    {
        abort(405, 'Il n\'est pas possible de créer un access');
    }

    /**
     * Show an access.
     *
     * @param  string $access_id
     * @return JsonResponse
     */
    public function show(string $access_id): JsonResponse
    {
        $access = Access::find($access_id);

        return response()->json($access->hideSubData(), 200);
    }

    /**
     * Update not possible (automatic generation)
     *
     * @param  string $access_id
     * @return void
     */
    public function update(string $access_id): void
    {
        abort(405, 'Il n\'est pas possible de modifier un access');
    }

    /**
     * Deletion not possible (automatic generation).
     *
     * @param  string $access_id
     * @return void
     */
    public function destroy(string $access_id): void
    {
        abort(405, 'Il n\'est pas possible de suprimer un accès');
    }
}

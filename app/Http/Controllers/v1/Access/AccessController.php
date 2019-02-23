<?php
/**
 * Gère les accès.
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
     * Récupération publique ou sous scopes.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne('user-get-access', 'client-get-access')
        );
    }

    /**
     * Liste les accès.
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
     * Création non possible.
     *
     * @return void
     */
    public function store(): void
    {
        abort(405, 'Il n\'est pas possible de créer un access');
    }

    /**
     * Montre un accès.
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
     * Mise à jour non possible (génération automatique).
     *
     * @param  string $access_id
     * @return void
     */
    public function update(string $access_id): void
    {
        abort(405, 'Il n\'est pas possible de modifier un access');
    }

    /**
     * Suppression non possible (génération automatique).
     *
     * @param  string $access_id
     * @return void
     */
    public function destroy(string $access_id): void
    {
        abort(405, 'Il n\'est pas possible de supprimer un access');
    }
}

<?php
/**
 * Gère les types de réservation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Booking;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\BookingType;

class BookingTypeController extends Controller
{
    /**
     * Récupération publique ou sous scopes.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne('user-get-bookings-types', 'client-get-bookings-types')
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
        $typeList = BookingType::getSelection()->map(function ($type) {
            return $type->hideData();
        });

        return response()->json($typeList, 200);
    }

    /**
     * Création non possible.
     *
     * @return void
     */
    public function store(): void
    {
        abort(405, 'Il n\'est pas possible de créer un type de réservation');
    }

    /**
     * Montre un accès.
     *
     * @param  string $type_id
     * @return JsonResponse
     */
    public function show(string $type_id): JsonResponse
    {
        $type = BookingType::find($type_id);

        return response()->json($type->hideSubData(), 200);
    }

    /**
     * Mise à jour non possible (génération automatique).
     *
     * @param  string $type_id
     * @return void
     */
    public function update(string $type_id): void
    {
        abort(405, 'Il n\'est pas possible de modifier un type de réservation');
    }

    /**
     * Suppression non possible (génération automatique).
     *
     * @param  string $type_id
     * @return void
     */
    public function destroy(string $type_id): void
    {
        abort(405, 'Il n\'est pas possible de supprimer un type de réservation');
    }
}

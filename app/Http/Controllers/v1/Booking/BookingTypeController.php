<?php
/**
 * Manages booking types.
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
     * Public retrievement or sub scopes.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOne('user-get-bookings-types', 'client-get-bookings-types')
        );
    }

    /**
     * Lists the accesses.
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
     * Creation not possible.
     *
     * @return void
     */
    public function store(): void
    {
        abort(405, 'Il n\'est pas possible de créer un type de réservation');
    }

    /**
     * Shows an access.
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
     * Update not possible (automatic generation).
     *
     * @param  string $type_id
     * @return void
     */
    public function update(string $type_id): void
    {
        abort(405, 'Il n\'est pas possible de modifier un type de réservation');
    }

    /**
     * Deletion not possible (automatic generation).
     *
     * @param  string $type_id
     * @return void
     */
    public function destroy(string $type_id): void
    {
        abort(405, 'Il n\'est pas possible de Deletesr un type de réservation');
    }
}

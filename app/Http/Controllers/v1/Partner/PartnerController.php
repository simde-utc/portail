<?php
/**
 * Manage partners.
 *
 * TODO: Move retrievement in a Trait.
 * TODO: Transform in abort.
 * TODO: Scopes !
 *
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Josselin Pennors <josselin.pennors@hotmail.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Partner;

use App\Http\Controllers\v1\Controller;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * List partners.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $partners = Partner::all()->map(function ($partner) {
            return $partner->hideData();
        });

        return response()->json($partners);
    }

    /**
     * Create a partner.
     *
     * @param PartnerRequest $request
     * @return JsonResponse
     */
    public function store(PartnerRequest $request): JsonResponse
    {
        $partner = Partner::create($request->input());

        return response()->json($partner->hideSubData());
    }

    /**
     * Show a partner.
     *
     * @param PartnerRequest $request
     * @param string         $partner_id
     * @return JsonResponse
     */
    public function show(PartnerRequest $request, string $partner_id): JsonResponse
    {
        $partner = Partner::find($partner_id);

        if ($partner) {
            return response()->json($partner->hideSubData());
        } else {
            abort(404, 'Le partenaire demandé n\'a pas été trouvé');
        }
    }

    /**
     * Update a partner.
     *
     * @param PartnerRequest $request
     * @param string         $partner_id
     * @return JsonResponse
     */
    public function update(PartnerRequest $request, string $partner_id): JsonResponse
    {
        $partner = Partner::find($partner_id);

        if ($partner) {
            $partner->update($request->input());

            return response()->json($partner->hideSubData());
        }

        abort(404, 'Le partenaire demandé n\'a pas été trouvé');
    }

    /**
     * Delete a partner.
     *
     * @param PartnerRequest $request
     * @param string         $partner_id
     * @return void
     */
    public function destroy(PartnerRequest $request, string $partner_id): void
    {
        $partner = Partner::find($partner_id);

        if ($partner) {
            $partner->delete();

            abort(204);
        } else {
            abort(404, 'Le partenaire demandé n\'a pas été trouvé');
        }
    }
}

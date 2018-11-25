<?php
/**
 * Gère les partenaires.
 *
 * TODO: Déplacer la récupération dans un Trait.
 * TODO: Transformer en abort.
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
     * Liste les partenaires.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $partners = Partner::all();

        if ($partners) {
            return response()->json($partners, 200);
        } else {
            return response()->json(['message' => 'Erreur'], 500);
        }
    }

    /**
     * Crée un partenaire.
     *
     * @param PartnerRequest $request
     * @return JsonResponse
     */
    public function store(PartnerRequest $request): JsonResponse
    {
        $partner = Partner::create($request->input());

        return response()->json($partner->hideSubData(), 200);
    }

    /**
     * Montre un partenaire.
     *
     * @param string $partner_id
     * @return JsonResponse
     */
    public function show(string $partner_id): JsonResponse
    {
        $partner = Partner::find($partner_id);

        if ($partner) {
            return response()->json($partner->hideSubData(), 200);
        } else {
            return response()->json(['message' => 'Le partenaire demandé n\'a pas été trouvé'], 404);
        }
    }

    /**
     * Met à jour un partenaire.
     *
     * @param PartnerRequest $request
     * @param  string         $partner_id
     * @return JsonResponse
     */
    public function update(PartnerRequest $request, string $partner_id): JsonResponse
    {
        $partner = Partner::find($partner_id);

        if ($partner) {
            if ($partner->update($request->input())) {
                if (Partner::where('name', $request->input('name'))->get()->first()
                && ($partner->name != $request->input('name'))) {
                    return response()->json('Ce partenaire existe déjà, conflit', 409);
                } else {
                    return response()->json($partner->hideSubData(), 200);
                }
            }

            return response()->json(['message' => 'Erreur pendant la mise à jour du partenaire'], 500);
        }

        return response()->json(['message' => 'Le partenaire demandé n\'a pas été trouvé'], 404);
    }

    /**
     * Supprime un partenaire.
     *
     * @param  string $partner_id
     * @return JsonResponse
     */
    public function destroy(string $partner_id): JsonResponse
    {
        $partner = Partner::find($partner_id);

        if ($partner) {
            if ($partner->delete()) {
                return response()->json(['message' => 'Le partenaire a bien été supprimé'], 200);
            } else {
                return response()->json(['message' => 'Erreur lors de la suppression du partenaire'], 500);
            }
        } else {
            return response()->json(['message' => 'Le partenaire demandé n\'a pas été trouvé'], 404);
        }
    }
}

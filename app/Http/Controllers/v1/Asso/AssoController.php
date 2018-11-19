<?php
/**
 * Gère les associations.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Asso;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AssoRequest;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasAssos;
use App\Traits\Controller\v1\HasImages;

class AssoController extends Controller
{
    use HasAssos, HasImages;

    /**
     * Nécessité de gérer les associations.
     * Lecture publique.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::allowPublic()->matchOne('user-get-assos', 'client-get-assos'),
	        ['only' => ['index', 'show']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-create-assos', 'client-create-assos'),
		        ['permission:asso']
	        ),
	        ['only' => ['store']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-edit-assos', 'client-edit-assos'),
		        ['permission:asso']
	        ),
	        ['only' => ['update']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-remove-assos', 'client-remove-assos'),
		        ['permission:asso']
	        ),
        	['only' => ['destroy']]
        );
    }

    /**
     * Liste les associations.
     *
     * @param AssoRequest $request
     * @return JsonResponse
     */
    public function index(AssoRequest $request): JsonResponse
    {
        $assos = Asso::getSelection()->map(function ($asso) {
            return $asso->hideData();
        });

        return response()->json($assos, 200);
    }

    /**
     * Ajoute une association.
     *
     * @param AssoRequest $request
     * @return JsonResponse
     */
    public function store(AssoRequest $request): JsonResponse
    {
        $asso = Asso::create($request->input());

        // On affecte l'image si tout s'est bien passé.
        $this->setImage($request, $asso, 'assos/'.$asso->id);

        // Après la création, on ajoute son président (non confirmé évidemment).
        $asso->assignRoles(config('portail.roles.admin.assos'), [
            'user_id' => $request->input('user_id'),
        ], true);

        // On met l'asso en état inactif (en attente de confirmation).
        $asso->delete();

        return response()->json($asso, 201);
    }

    /**
     * Montre une association.
     *
     * @param AssoRequest $request
     * @param string      $asso_id
     * @return JsonResponse
     */
    public function show(Request $request, string $asso_id): JsonResponse
    {
        $asso = $this->getAsso($request, $asso_id);

        return response()->json($asso->hideSubData(), 200);
    }

    /**
     * Met à jour une association.
     *
     * @param AssoRequest $request
     * @param string      $asso_id
     * @return JsonResponse
     */
    public function update(AssoRequest $request, string $asso_id): JsonResponse
    {
        $asso = $this->getAsso($request, $asso_id);

        if (isset($request['validate'])) {
            $asso->updateRoles(config('portail.roles.admin.assos'), [
                'validated_by' => null,
            ], [
                'validated_by' => \Auth::id(),
            ], $asso->getLastUserWithRole(config('portail.roles.admin.assos'))->id === \Auth::id());

            return response()->json($asso, 200);
        }

        if (!$asso->hasOnePermission('asso_data', ['user_id' => \Auth::id()]) && !\Auth::user()->hasOneRole('admin')) {
            abort(403, 'Il est nécessaire de posséder les droits pour pouvoir modifier cette association');
        }

        if (isset($request['restore'])) {
            if (!\Auth::user()->hasOnePermission('asso')) {
                abort(403, 'Il est nécessaire de posséder les droits associations pour pouvoir restaurer cette association');
            }

            $asso->restore();
        }

        if ($asso->update($request->input())) {
            // On affecte l'image si tout s'est bien passé.
            $this->setImage($request, $asso, 'assos/'.$asso->id);

            return response()->json($asso, 200);
        } else {
            abort(500, 'L\'association n\'a pas pu être modifiée');
        }
    }

    /**
     * Supprime une association.
     *
     * @param AssoRequest $request
     * @param string      $asso_id
     * @return void
     */
    public function destroy(Request $request, string $asso_id): void
    {
        $asso = $this->getAsso($request, $asso_id);

        if ($asso->children()->exists()) {
            abort(400, 'Il n\'est pas possible de supprimer une association parente');
        }

        $asso->delete();
        $this->deleteImage('assos/'.$asso->id);

        abort(204);
    }
}

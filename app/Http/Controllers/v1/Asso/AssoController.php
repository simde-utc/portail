<?php
/**
 * Manage associations.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
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
     * Must be able to manage associations.
     * Public access.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::allowPublic()->matchOne('user-get-assos', 'client-get-assos'),
	        ['only' => ['all', 'get']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-create-assos', 'client-create-assos'),
		        ['permission:asso']
	        ),
	        ['only' => ['create']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-edit-assos', 'client-edit-assos'),
		        ['permission:asso']
	        ),
	        ['only' => ['edit']]
        );
        $this->middleware(
	        array_merge(
		        \Scopes::matchOne('user-remove-assos', 'client-remove-assos'),
		        ['permission:asso']
	        ),
        	['only' => ['remove']]
        );
    }

    /**
     * List associations.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $assos = Asso::with('parent');

        // If GET parameter `cemetary` exists and is true, we add associations in cemetery.
        if (!($request->input('cemetery') != null && $request->input('cemetery') === 'true')) {
            $assos = $assos->whereNull('in_cemetery_at');
        }

        $assos = $assos->getSelection()->map(function ($asso) {
            return $asso->hideData();
        });

        return response()->json($assos, 200);
    }

    /**
     * Add an association.
     *
     * @param AssoRequest $request
     * @return JsonResponse
     */
    public function store(AssoRequest $request): JsonResponse
    {
        $asso = Asso::create($request->input());

        // Affecting image if everything went well.
        $this->setImage($request, $asso, 'assos/'.$asso->id);

        // After the creation, the president is added (not confirmed).
        $asso->assignRoles(config('portail.roles.admin.assos'), [
            'user_id' => $request->input('user_id'),
        ], true);

        // If we put the asso in a inactive state (waiting for confirmation).
        $asso->delete();

        return response()->json($asso, 201);
    }

    /**
     * Show an association.
     *
     * @param Request $request
     * @param string  $asso_id
     * @return JsonResponse
     */
    public function show(Request $request, string $asso_id): JsonResponse
    {
        $asso = $this->getAsso($request, $asso_id);

        return response()->json($asso->hideSubData(), 200);
    }

    /**
     * Update an association.
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
                'validated_by_id' => null,
            ], [
                'validated_by_id' => \Auth::id(),
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
            // Affecting image if everything went well.
            $this->setImage($request, $asso, 'assos/'.$asso->id);

            return response()->json($asso, 200);
        } else {
            abort(500, 'L\'association n\'a pas pu être modifiée');
        }
    }

    /**
     * Delete an association.
     *
     * @param Request $request
     * @param string  $asso_id
     * @return void
     */
    public function destroy(Request $request, string $asso_id): void
    {
        $asso = $this->getAsso($request, $asso_id);

        if ($asso->children()->exists()) {
            abort(400, 'Il n\'est pas possible de suprimer an association parente');
        }

        $asso->delete();
        // An asso is not really deleted: $this->deleteImage('assos/'.$asso->id);.
        abort(204);
    }
}

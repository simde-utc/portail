<?php
/**
 * Gestion des associations de l'utilisateur.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserAssoRequest;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\{
    HasUserBulkMethods, HasAssos
};

class AssoController extends Controller
{
    use HasUserBulkMethods, HasAssos;

    /**
     * Nécessité de pouvoir gérer les associations de l'utilisateur.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-assos-members', 'client-get-assos-members'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-assos-members', 'client-create-assos-members'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-assos-members', 'client-edit-assos-members'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-remove-assos-members', 'client-remove-assos-members'),
            ['only' => ['destroy']]
        );
        // Can index, show and create, edit and remove assos for multiple users in a raw.
        $this->middleware(
            \Scopes::matchAnyClient(),
            ['only' => ['bulkIndex', 'bulkStore', 'bulkShow', 'bulkUpdate', 'bulkDestroy']]
        );
    }

    /**
     * Liste des associations de l'utlisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);
        $choices = $this->getChoices($request, ['joined', 'joining', 'followed']);
        $semester = $this->getSemester($request, $choices);

        $assos = collect();

        if (in_array('joined', $choices)) {
            $assos = $assos->merge($user->joinedAssos()->where('semester_id', $semester->id)->get());
        }

        if (in_array('joining', $choices)) {
            $assos = $assos->merge($user->joiningAssos()->where('semester_id', $semester->id)->get());
        }

        if (in_array('followed', $choices)) {
            $assos = $assos->merge($user->followedAssos()->where('semester_id', $semester->id)->get());
        }

        return response()->json($assos->map(function ($asso) {
            return $asso->hideData();
        }), 200);
    }

    /**
     * Ajoute une association suivie par l'utilisateur.
     *
     * @param UserAssoRequest $request
     * @param string          $user_id
     * @return JsonResponse
     */
    public function store(UserAssoRequest $request, string $user_id=null): JsonResponse
    {
        $user = $this->getUser($request, $user_id);
        $semester = $this->getSemester($request, ['followed'], 'create');
        $asso = $this->getAsso($request, $request->input('asso_id'));
        $scopeHead = \Scopes::getTokenType($request);

        $asso->assignMembers(\Auth::id(), [
            'semester_id' => $semester->id,
        ]);

        $asso = $this->getAsso($request, $asso->id, $user, $semester);

        return response()->json($asso->hideSubData(), 201);
    }

    /**
     * Montre une association suivie par l'utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $asso_id
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $asso_id=null): JsonResponse
    {
        if (is_null($asso_id)) {
            list($user_id, $asso_id) = [$asso_id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $semester = $this->getSemester($request, ['followed']);
        $asso = $this->getAsso($request, $asso_id, $user, $semester);

        return response()->json($asso->hideSubData(), 200);
    }

    /**
     * Il n'est pas possible de mettre à jour.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $asso_id
     * @return void
     */
    public function update(Request $request, string $user_id, string $asso_id=null)
    {
        abort(405);
    }

    /**
     * Retire une association suivie par l'utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $asso_id
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $asso_id=null)
    {
        if (is_null($asso_id)) {
            list($user_id, $asso_id) = [$asso_id, $user_id];
        }

        $user = $this->getUser($request, $user_id);
        $semester = $this->getSemester($request, ['followed']);
        $asso = $this->getAsso($request, $asso_id, $user, $semester);

        if ($asso->removeMembers($user, [
            'semester_id' => $asso->pivot->semester_id,
        ], \Auth::id(), \Scopes::isClientToken($request))) {
            abort(204);
        } else {
            abort(500, 'Impossible de retirer la personne de l\'association');
        }
    }
}

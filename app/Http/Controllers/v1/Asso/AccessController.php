<?php
/**
 * Gère les accès des assos.
 *
 * TODO: Exporter dans un Trait
 * TODO: Scopes manquant !
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Asso;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asso;
use App\Models\Semester;
use App\Models\Role;
use App\Models\AssoAccess;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasAssos;

class AccessController extends Controller
{
    use HasAssos;

    /**
     * Nécessité de gérer les accès des associations.
     */
    public function __construct()
    {
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Pouvoir voir les assos.
            ),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Pouvoir voir les assos.
            ),
            ['only' => ['store']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Pouvoir voir les assos.
            ),
            ['only' => ['update']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Pouvoir voir les assos.
            ),
            ['only' => ['destroy']]
        );
    }

    /**
     * Récupération des accès.
     *
     * @param  Request $request
     * @param  string  $access_id
     * @param  string  $user_id
     * @param  Asso    $asso
     * @param  string  $semester_id
     * @return AssoAccess|void
     */
    protected function getAccess(Request $request, string $access_id, string $user_id=null, Asso $asso, string $semester_id)
    {
        $access = AssoAccess::where('id', $access_id)
            ->where('asso_id', $asso->id)
            ->where('semester_id', $semester_id);

        if ($user_id && !$asso->hasOnePermission('access', [ 'user_id' => $user_id ])) {
            $access = $access->where(function ($query) use ($user_id) {
                return $query->whereNotNull('validated_at')->orWhere('member_id', $user_id);
            });
        }

        $access = $access->first();

        if ($access) {
            return $access;
        } else {
            abort(404, 'Demande d\'accès non existante');
        }
    }

    /**
     * Liste des accès.
     *
     * @param Request $request
     * @param string  $asso_id
     * @return JsonResponse
     */
    public function index(Request $request, string $asso_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $asso = $this->getAssoFromMember($request, $asso_id, \Auth::id(), $semester);
        $access = $asso->access()->where('semester_id', $semester->id);

        if (\Auth::id() && !$asso->hasOnePermission('access', [ 'user_id' => \Auth::id() ])) {
            $access = $access->where(function ($query) {
                return $query->whereNotNull('validated_at')->orWhere('member_id', \Auth::id());
            });
        }

        $access = $access->getSelection()
            ->map(function ($access) {
                return $access->hideData();
            });

        return response()->json($access, 200);
    }

    /**
     * Crée une demande d'accès.
     *
     * @param Request $request
     * @param string  $asso_id
     * @return JsonResponse
     */
    public function store(Request $request, string $asso_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $user_id = (\Auth::id() ?? $request->input('user_id'));
        $asso = $this->getAssoFromMember($request, $asso_id, \Auth::id(), $semester);

        if ($asso->access()->where('semester_id', $semester->id)
            ->whereNotNull('validated_at')
            ->where('member_id', $user_id)
            ->count() > 0) {
            throw new PortailException("Une demande d\'accès a déjà été validée pour ce semestre");
        }

        $comment = $request->input('comment');

        if (!trim($comment)) {
            throw new PortailException("Il est nécessaire de donner une raison précise de la demande");
        }

        $access = $asso->access()->create([
            'member_id' => $user_id,
            'access_id' => $request->input('access_id'),
            'semester_id' => $semester->id,
            'description' => $request->input('description'),
        ]);

        return response()->json(AssoAccess::find($access->id)->hideSubData(), 201);
    }

    /**
     * Montre une demande d'accès.
     *
     * @param Request $request
     * @param string  $asso_id
     * @param string  $access_id
     * @return JsonResponse
     */
    public function show(Request $request, string $asso_id, string $access_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $user_id = (\Auth::id() ?? $request->input('user_id'));
        $asso = $this->getAssoFromMember($request, $asso_id, \Auth::id(), $semester);
        $access = $this->getAccess($request, $access_id, $user_id, $asso, $semester->id);

        return response()->json($access->hideSubData(), 200);
    }

    /**
     * Met à jour une demande d'accès.
     *
     * @param Request $request
     * @param string  $asso_id
     * @param string  $access_id
     * @return JsonResponse
     */
    public function update(Request $request, string $asso_id, string $access_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $user_id = (\Auth::id() ?? $request->input('user_id'));
        $asso = $this->getAssoFromMember($request, $asso_id, \Auth::id(), $semester);
        $access = $this->getAccess($request, $access_id, $user_id, $asso, $semester->id);

        // On doit valider au moins la demande d'accès.
        if (!$access->confirmed_by_id) {
            if (!$asso->hasOnePermission('access', [ 'user_id' => $user_id ])) {
                $access->confirmed_by_id = $user_id;
            } else {
                abort(403, "Il ne vous est pas autorisé de confirmer la demande");
            }
        } else if (!$access->validated_by_id) {
            if (!User::find($user_id)->hasOnePermission('access')) {
                $access->validated_by_id = $user_id;
                $access->validated_at = now();
                $access->validated = $request->input('validate');
                $access->comment = $request->input('comment');
            } else {
                abort(403, "Il ne vous est pas autorisé de valider ou refuser cette demande");
            }
        } else {
            abort(400, "Aucune action n'est possible");
        }

        $access->save();

        return response()->json($access->hideSubData(), 200);
    }

    /**
     * Supprime une demande d'accès.
     *
     * @param Request $request
     * @param string  $asso_id
     * @param string  $access_id
     * @return void
     */
    public function destroy(Request $request, string $asso_id, string $access_id): void
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $user_id = (\Auth::id() ?? $request->input('user_id'));
        $asso = $this->getAssoFromMember($request, $asso_id, \Auth::id(), $semester);
        $access = $this->getAccess($request, $access_id, $user_id, $asso, $semester->id);

        if ($access->member_id = $user_id && $access->validated_by_id === null) {
            $access->delete();
            abort(204);
        } else {
            abort(500, 'Il n\'est plus possible d\'annuler la demande d\'accès');
        }
    }
}

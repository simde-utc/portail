<?php
/**
 * Manages association accesses.
 *
 * TODO: Export into a Trait
 * TODO: Missing Scopes !
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
use App\Http\Requests\AccessRequest;

class AccessController extends Controller
{
    use HasAssos;

    /**
     * Must be able to manage the accesses of the associations.
     */
    public function __construct()
    {
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Be able to see associations.
            ),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Be able to see associations.
            ),
            ['only' => ['store']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Be able to see associations.
            ),
            ['only' => ['update']]
        );
        $this->middleware(
            array_merge(
                \Scopes::matchOneOfDeepestChildren('user-get-assos', 'client-get-assos')
                // Be able to see associations.
            ),
            ['only' => ['destroy']]
        );
    }

    /**
     * Accesses retrievement.
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

        if ($user_id && !$asso->hasOnePermission('access', ['user_id' => $user_id])) {
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
     * Lists some accesses.
     *
     * @param Request $request
     * @param string  $asso_id
     * @return JsonResponse
     */
    public function index(Request $request, string $asso_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $asso = $this->getAsso($request, $asso_id, \Auth::user(), $semester);
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
     * Creates an access demand.
     *
     * @param AccessRequest $request
     * @param string        $asso_id
     * @return JsonResponse
     */
    public function store(AccessRequest $request, string $asso_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $user_id = (\Auth::id() ?? $request->input('user_id'));
        $asso = $this->getAsso($request, $asso_id, \Auth::user(), $semester);
        $countAccess = $asso->access()->where('semester_id', $semester->id)
        ->where('member_id', $user_id)
            ->where(function ($query) {
                $query->where(function ($query) {
                    return $query->whereNull('validated_at');
                })->orWhere(function ($query) {
                          return $query->where('validated', true)
                    ->whereNotNull('validated_at');
                });
            })
            ->count();

        if ($countAccess > 0) {
            throw new PortailException("Une demande d\'accès a déjà été validée ou est en cours pour ce semestre");
        }

        $description = $request->input('description');

        if (!trim($description)) {
            throw new PortailException("Il est nécessaire de donner une raison précise de la demande");
        }

        $access = $asso->access()->create([
            'member_id' => $user_id,
            'access_id' => $request->input('access_id'),
            'semester_id' => $semester->id,
            'description' => $description,
        ]);

        return response()->json(AssoAccess::find($access->id)->hideSubData(), 201);
    }

    /**
     * Shows an access demand.
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
        $asso = $this->getAsso($request, $asso_id, \Auth::user(), $semester);
        $access = $this->getAccess($request, $access_id, $user_id, $asso, $semester->id);

        return response()->json($access->hideSubData(), 200);
    }

    /**
     * Updates an access demand.
     *
     * @param AccessRequest $request
     * @param string        $asso_id
     * @param string        $access_id
     * @return JsonResponse
     */
    public function update(AccessRequest $request, string $asso_id, string $access_id): JsonResponse
    {
        $choices = $this->getChoices($request);
        $semester = $this->getSemester($request, $choices);
        $user_id = (\Auth::id() ?? $request->input('user_id'));
        $asso = $this->getAsso($request, $asso_id, \Auth::user(), $semester);
        $access = $this->getAccess($request, $access_id, $user_id, $asso, $semester->id);

        // We must validate at least the access demand.
        if (!$access->confirmed_by_id) {
            if ($asso->hasOnePermission('access', [ 'user_id' => $user_id ])) {
                $access->confirmed_by_id = $user_id;
            } else {
                abort(403, "Il ne vous est pas autorisé de confirmer la demande");
            }
        } else if (!$access->validated_by_id) {
            if ($request->filled('validate') && $request->filled('comment')) {
                $comment = $request->input('comment');

                if (!trim($comment)) {
                    throw new PortailException("Il est nécessaire de donner une raison précise de la validation ou du refus");
                }

                if (User::find($user_id)->hasOnePermission('access')) {
                    $access->validated_by_id = $user_id;
                    $access->validated_at = now();
                    $access->validated = $request->input('validate');
                    $access->comment = $comment;
                } else {
                    abort(403, "Il ne vous est pas autorisé de valider ou refuser cette demande");
                }
            } else {
                abort(403, "Il est nécessaire d'indiquer si la demande est validée et de commenter");
            }
        } else {
            abort(400, "Aucune action n'est possible");
        }

        $access->save();

        return response()->json($access->refresh()->hideSubData());
    }

    /**
     * Deletes an access demand.
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
        $asso = $this->getAsso($request, $asso_id, \Auth::user(), $semester);
        $access = $this->getAccess($request, $access_id, $user_id, $asso, $semester->id);

        if ($access->member_id = $user_id && $access->validated_by_id === null) {
            $access->delete();
            abort(204);
        } else {
            abort(500, 'Il n\'est plus possible d\'annuler la demande d\'accès');
        }
    }
}

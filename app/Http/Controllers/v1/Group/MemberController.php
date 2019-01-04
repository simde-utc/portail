<?php
/**
 * Gère les membres des groupes.
 *
 * TODO: Scopes !
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Group;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\GroupMemberRequest;
use App\Models\Group;
use App\Exceptions\PortailException;
use Illuminate\Support\Collection;
use App\Traits\Controller\v1\HasGroups;

class MemberController extends Controller
{
    use HasGroups;

    /**
     * Nécessité de gérer les groupes.
     */
    public function __construct()
    {
        $this->middleware(
	        \Scopes::matchOneOfDeepestChildren('user-get-groups', 'client-get-groups'),
	        ['only' => ['index', 'show']]
        );
        $this->middleware(
        	\Scopes::matchOneOfDeepestChildren('user-manage-groups', 'client-manage-groups'),
        	['only' => ['store', 'update', 'destroy']]
        );
    }

    /**
     * Liste les membres du groupe.
     *
     * @param Request $request
     * @param string  $group_id
     * @return JsonResponse
     */
    public function index(Request $request, string $group_id): JsonResponse
    {
        $groups = $this->getGroup($request, $group_id)->currentAllMembers()->getSelection();

        return response()->json($groups->map(function ($member) {
            return $member->hideData();
        }));
    }

    /**
     * Ajouter un membre au groupe.
     *
     * @param GroupMemberRequest $request
     * @param string             $group_id
     * @return JsonResponse
     */
    public function store(GroupMemberRequest $request, string $group_id): JsonResponse
    {
        $group = $this->getGroup($request, $group_id);

        $data = [
            'semester_id' => $request->input('semester_id', '0'),
            'role_id'     => $request->input('role_id', null),
        ];
        // TODO: Envoyer un mail d'invitation dans le groupe.
        try {
            $group->assignMembers($request->input('member_ids', (array) $request->input('member_id')), $data);
        } catch (PortailException $e) {
            abort(400, $e->getMessage());
        }

        $members = $group->currentAllMembers->map(function ($member) {
            return $member->hideData();
        });

        return response()->json($members);
    }

    /**
     * Montre un membre du groupe.
     *
     * @param Request $request
     * @param string  $group_id
     * @param string  $member_id
     * @return JsonResponse
     */
    public function show(Request $request, string $group_id, string $member_id): JsonResponse
    {
        $group = $this->getGroup($request, $group_id);
        $member = $group->currentAllMembers()->where('id', $member_id)->first();

        if ($member) {
            return response()->json($member->hideData());
        } else {
            abort(404, 'Cette personne ne fait pas partie du groupe');
        }
    }

    /**
     * Met à jour un membre du groupe.
     *
     * @param GroupMemberRequest $request
     * @param string             $group_id
     * @param string             $member_id
     * @return JsonResponse
     */
    public function update(GroupMemberRequest $request, string $group_id, string $member_id): JsonResponse
    {
        $group = $this->getGroup($request, $group_id);
        $member = $group->currentAllMembers->where('id', $member_id)->first();

        if ($member) {
            if ($member_id === (string) \Auth::id()) {
                $data = [
                    'semester_id'  => $request->input('semester_id', $member->pivot->semester_id),
                    'role_id'      => $request->input('role_id', $member->pivot->role_id),
                    'validated_by' => $member_id,
                ];
            } else {
                $data = [
                    'semester_id' => $request->input('semester_id', $member->pivot->semester_id),
                    'role_id'     => $request->input('role_id', $member->pivot->role_id),
                ];
                // TODO: Envoyer un mail d'invitation dans le groupe.
            }

            try {
                $group->updateMembers($member_id, [
                    'semester_id' => $member->pivot->semester_id,
                ], $data);
            } catch (PortailException $e) {
                abort(400, $e->getMessage());
            }

            return response()->json($group->currentAllMembers()->where('user_id', $member_id)->first()->hideData());
        } else {
            abort(404, 'Cette personne ne fait pas partie du groupe');
        }
    }

    /**
     * Supprime un membre du groupe.
     *
     * @param Request $request
     * @param string  $group_id
     * @param string  $member_id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $group_id, string $member_id): JsonResponse
    {
        $group = $this->getGroup($request, $group_id);
        $member = $group->currentAllMembers->where('id', $member_id)->first();

        if ($member) {
            $data = [
                'semester_id' => $request->input('semester_id', '0'),
            ];

            $group->removeMembers($member_id, $data, \Auth::id());

            $members = $group->currentAllMembers->map(function ($member) {
                return $member->hideData();
            });

            return response()->json($members);
        } else {
            abort(404, 'Cette personne ne faisait déjà pas partie du groupe');
        }
    }
}

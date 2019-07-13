<?php
/**
 * Manages groups.
 *
 * TODO: Remake scopes.
 * TODO: Export in a Trait.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Group;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\{
    Group, Visibility
};
use App\Http\Requests\GroupRequest;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasGroups;

class GroupController extends Controller
{
    use HasGroups;

    /**
     * Must be able to manage groups.
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
     * Lists groups.
     *
     * @param GroupRequest $request
     * @return JsonResponse
     */
    public function index(GroupRequest $request): JsonResponse
    {
        // Relations inclusion anf formatting them.
        $groups = Group::getSelection();

        return response()->json($groups->map(function ($group) {
            return $group->hideData();
        }), 200);
    }

    /**
     * Creates a group.
     *
     * @param GroupRequest $request
     * @return JsonResponse
     */
    public function store(GroupRequest $request): JsonResponse
    {
        $group = new Group;
        $group->user_id = \Auth::id();
        $group->name = $request->name;
        $group->icon = $request->icon;
        $group->visibility_id = ($request->visibility_id ?? Visibility::findByType('private')->id);

        if ($group->save()) {
            /*
                The group creator becomes automatically admin and member of its group.
                Members ids to add will be passed into the request.
                Ids is an array of user ids.
            */

            // TODO: Send an invitation to the group email.
            try {
                $group->assignMembers($request->input('member_ids', []), [
                    'semester_id' => $request->input('semester_id', '0'),
                ]);
            } catch (PortailException $e) {
                return response()->json(["message" => $e->getMessage()], 400);
            }

            $group = Group::with(['owner', 'visibility'])->find($group->id);

            return response()->json($group->hideData(), 201);
        } else {
            abort(500, 'Impossible de créer le groupe');
        }
    }

    /**
     * Shows a group.
     *
     * @param GroupRequest $request
     * @param string       $group_id
     * @return JsonResponse
     */
    public function show(GroupRequest $request, string $group_id): JsonResponse
    {
        $group = $this->getGroup($request, $group_id);

        return response()->json($group->hideSubData(), 200);
    }

    /**
     * Updates a group.
     *
     * @param GroupRequest $request
     * @param string       $group_id
     * @return JsonResponse
     */
    public function update(GroupRequest $request, string $group_id): JsonResponse
    {
        $group = $this->getGroup($request, $group_id);

        if ($request->filled('user_id')) {
            $group->user_id = $request->input('user_id');
        }

        if ($request->filled('name')) {
            $group->name = $request->input('name');
        }

        if ($request->filled('icon')) {
            $group->icon = $request->input('icon');
        }

        if ($request->filled('visibility_id')) {
            $group->visibility_id = $request->input('visibility_id');
        }

        if ($group->save()) {
            if ($request->filled('member_ids')) {
                try {
                    $group->syncMembers(array_merge($request->member_ids, [\Auth::id()]), [
                        'semester_id' => $request->input('semester_id', '0'),
                        'removed_by'  => $group->user_id,
                    ], \Auth::id());
                } catch (PortailException $e) {
                    return response()->json(["message" => $e->getMessage()], 400);
                }
            }

            $group = Group::with(['owner', 'visibility'])->find($group_id);

            return response()->json($group->hideSubData(), 200);
        } else {
            abort(500, 'Impossible de modifier le groupe');
        }
    }

    /**
     * Deletes a group.
     *
     * @param GroupRequest $request
     * @param string       $group_id
     * @return void
     */
    public function destroy(GroupRequest $request, string $group_id): void
    {
        $group = $this->getGroup($request, $group_id);
        $group->delete();

        abort(204);
    }
}

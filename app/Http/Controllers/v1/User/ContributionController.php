<?php
/**
 * Manage the user contributions.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Traits\Controller\v1\HasSemesters;
use App\Traits\Controller\v1\HasUsers;
use Illuminate\Support\Arr;

class ContributionController extends Controller
{
    use HasUsers, HasSemesters;

    /**
     * Must be able to get user contributions.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-contributions', 'client-get-contributions'),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
        \Scopes::matchOneOfDeepestChildren('user-create-contributions', 'client-create-contributions'),
	        ['only' => ['create']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-contributions', 'client-edit-contributions'),
	        ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-remove-contributions', 'client-remove-contributions'),
        	['only' => ['remove']]
        );
    }

    /**
     * List contributions of the current user.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);
        $ginger = \Ginger::userByEmail($user->email);
        $contributions = [];
        $semesters = Semester::get()
            ->map(function($semester){
                return $semester->hideData();
            })->toArray();

        foreach ($ginger->getContributions() as $contribution) {
            $contributionSemesters = array_filter($semesters, function ($semester) use ($contribution) {
                if (($semester['begin_at'] < $contribution->end_at) && ($semester['end_at'] > $contribution->begin_at)) {
                    return true;
                }
            });

            $newContribution = [
                'start' => $contribution->begin_at,
                'end' => $contribution->end_at,
                'amount' => $contribution->money,
                'semesters' => array_values($contributionSemesters),

            ];

            $contributions[] = $newContribution;
        }

        return response()->json($contributions);
    }

    /**
     * Show a contribution depending on a given semester range and an optionnel user.
     *
     * @param Request $request
     * @param string  $semester_id Work with id or name.
     * @param string  $user_id
     * @return JsonResponse
     */
    public function show(Request $request, string $semester_id="current", string $user_id=null)
    {
        $semester = $this->getSemester($semester_id, true);
        $user = $this->getUser($request, $user_id);
        $contributions = \Ginger::userByEmail($user->email)->getContributions();

        $contribution = Arr::first($contributions, function ($contribution) use ($semester){
            $dateToContain = ((strtotime($semester->begin_at) + strtotime($semester->end_at)) / 2);
            $contributionStart = strtotime($contribution->begin_at);
            $contributionEnd = strtotime($contribution->end_at);

            if ($contributionStart < $dateToContain && $dateToContain < $contributionEnd) {
                return true;
            }
        });

        if (!is_null($contribution)) {
            $semesters[] = $semester;
            if ($semester->is_spring) {
                array_unshift($semesters, $this->getSemester(
                    "A".strval(intval(substr($semester->name, -2)) - 1),
                    true
                ));
            } else {
                $semesters[] = $this->getSemester(
                    "P".strval(intval(substr($semester->name, -2)) + 1),
                    true
                );
            }

            $response = [
                'start'     => $contribution->begin_at,
                'end'       => $contribution->end_at,
                'amount'    => $contribution->money,
                "semesters" => $semesters,
            ];
        } else {
            $response = [
                "message" => "Contribution not found.",
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * Store not possible (not handled by the portal).
     *
     * @param string $semester_id Work with id or name.
     * @return void
     */
    public function store(string $semester_id="current"): void
    {
        abort(405, "Impossible to create a contribution.");
    }

    /**
     * Update not possible (not handled by the portal).
     *
     * @param string $semester_id Work with id or name.
     * @return void
     */
    public function update(string $semester_id="current"): void
    {
        abort(405, "Impossible to update a contribution.");
    }

    /**
     * Deletion not possible (not handled by the portal).
     *
     * @param string $semester_id Work with id or name.
     * @return void
     */
    public function delete(string $semester_id="current"): void
    {
        abort(405, "Impossible to delete a contribution.");
    }
}

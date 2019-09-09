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
use App\Traits\Controller\v1\HasUsers;

class ContributionController extends Controller
{
    use HasUsers;

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
                $dateToContain = ((strtotime($semester['begin_at']) + strtotime($semester['end_at']) ) / 2);
                if (( $dateToContain > strtotime($contribution->begin_at) ) &&
                    ( $dateToContain < strtotime($contribution->end_at) )
                ) {
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
     * @return void
     */
    public function show(Request $request, string $semester_id=null, string $user_id=null)
    {
        abort(405, "We found unuseful to get a contribution.");

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

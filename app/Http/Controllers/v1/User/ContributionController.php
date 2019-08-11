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
            \Scopes::matchOneOfDeepestChildren('user-get-contributions'),
            ['only' => ['index', 'show']]
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

        foreach ($ginger->getContributions() as $contribution) {
            $semesters = Semester::whereDate('begin_at', '<', $contribution->end_at)
                ->whereDate('end_at', '>', $contribution->begin_at)
                ->orderBy('begin_at')->get()->map(function ($semester) {
                    return $semester->hideData();
                });

            $contribution = [
                'start' => $contribution->begin_at,
                'end' => $contribution->end_at,
                'amount' => $contribution->money,
                'semesters' => $semesters,

            ];

            array_push($contributions, $contribution);
        }

        return response()->json($contributions);
    }
}

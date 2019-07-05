<?php
/**
 * Gère les détails des utilisateurs.
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
use App\Http\Requests\UserDetailRequest;
use App\Models\User;
use App\Models\UserDetail;
use App\Exceptions\PortailException;
use App\Traits\Controller\v1\HasUsers;

class DetailController extends Controller
{
    use HasUsers;

    /**
     * Nécessité de pouvoir gérer les détails des utilisateurs.
     * Avec Token user uniquement.
     */
    public function __construct()
    {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-info-details'),
            ['only' => ['all', 'get']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-info-details'),
            ['only' => ['create']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-info-details'),
            ['only' => ['edit']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-info-details'),
            ['only' => ['remove']]
        );
    }

    /**
     * Permet de vérifier le scope et le détail donné.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $key
     * @param string                   $verb
     * @return void
     */
    protected function checkScope(Request $request, string $key, string $verb)
    {
        try {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-details-'.$key)) {
                abort(403, 'Vous n\'avez pas les droits sur cette information');
            }
        } catch (PortailException $e) {
            abort(403, 'Il n\'existe pas de détail utilisateur de ce nom: '.$key);
        }
    }

    /**
     * Liste les détails de l'utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @return JsonResponse
     */
    public function index(Request $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);

        // On affiche chaque détail sous forme clé => valeur.
        return response()->json($user->details()->allToArray());
    }

    /**
     * Créer un détail pour l'utilisateur.
     *
     * @param UserDetailRequest $request
     * @param string            $user_id
     * @return JsonResponse
     */
    public function store(UserDetailRequest $request, string $user_id=null)
    {
        $user = $this->getUser($request, $user_id);

        if (\Scopes::isUserToken($request)) {
            $detail = UserDetail::create(array_merge(
                $request->input(),
                ['user_id' => $user->id]
            ));

            return response()->json($detail, 201);
        }
    }

    /**
     * Montre un détail pour l'utilisateur.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $key
     * @return JsonResponse
     */
    public function show(Request $request, string $user_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $key) = [$key, $user_id];
        }

        $this->checkScope($request, $key, 'get');
        $user = $this->getUser($request, $user_id);

        try {
            return response()->json($user->details()->toArray($key));
        } catch (PortailException $e) {
            abort(404, 'Cette personne ne possède pas ce détail');
        }
    }

    /**
     * Met à jour un détail pour l'utilisateur.
     *
     * @param UserDetailRequest $request
     * @param string            $user_id
     * @param string            $key
     * @return JsonResponse
     */
    public function update(UserDetailRequest $request, string $user_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $key) = [$key, $user_id];
        }

        $this->checkScope($request, $key, 'edit');
        $user = $this->getUser($request, $user_id);

        if (\Scopes::isUserToken($request)) {
            try {
                $detail = $user->details()->key($key);
                $detail->value = $request->input('value', $detail->value);

                if ($detail->update()) {
                    return response()->json($detail);
                } else {
                    abort(503, 'Erreur lors de la modification');
                }
            } catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être modifié');
            }
        }
    }

    /**
     * Supprime une détail pour l'utilisateur s'il est supprimable.
     *
     * @param Request $request
     * @param string  $user_id
     * @param string  $key
     * @return void
     */
    public function destroy(Request $request, string $user_id, string $key=null)
    {
        if (is_null($key)) {
            list($user_id, $key) = [$key, $user_id];
        }

        $this->checkScope($request, $key, 'manage');
        $user = $this->getUser($request, $user_id);

        if (\Scopes::isUserToken($request)) {
            try {
                $detail = $user->details()->key($key);

                if ($detail->delete()) {
                    abort(204);
                } else {
                    abort(503, 'Erreur lors de la suppression');
                }
            } catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être supprimé');
            }
        }
    }
}

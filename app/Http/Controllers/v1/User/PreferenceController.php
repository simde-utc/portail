<?php

namespace App\Http\Controllers\v1\User;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPreference;
use App\Traits\Controller\v1\HasUsers;

class PreferenceController extends Controller
{
    use HasUsers;

    public function __construct() {
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-get-info-preferences'),
            ['only' => ['index', 'show']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-create-info-preferences'),
            ['only' => ['store']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-edit-info-preferences'),
            ['only' => ['update']]
        );
        $this->middleware(
            \Scopes::matchOneOfDeepestChildren('user-manage-info-preferences'),
            ['only' => ['destroy']]
        );
    }

    protected function getPreferences(Request $request, $user, string $verb) {
        $choices = $this->getChoices($request, ['global', 'asso', 'client']);
        $token = $request->user() ? $request->user()->token() : $request->token();
        $client = $token->client;

        if (in_array('asso', $choices)) {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-preferences-asso'))
                abort(403, 'Vous n\'avez pas les droits sur les préférences de l\'association');

            $choices[array_search('asso', $choices)] = 'asso-'.$client->asso_id;
        }

        if (in_array('client', $choices)) {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-preferences-client'))
                abort(403, 'Vous n\'avez pas les droits sur les préférences du client');

            $choices[array_search('client', $choices)] = 'client-'.$client->id;
        }

        if (in_array('global', $choices)) {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-preferences-global'))
                abort(403, 'Vous n\'avez pas les droits sur les préférences globales de l\'utilisateur');
        }

        return $user->preferences()->whereIn('only_for', $choices);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);
        $groups = $this->getPreferences($request, $user, 'get')->get()->groupBy('only_for');
        $array = [];

        foreach ($groups as $only_for => $preferences) {
            $array[$only_for] = [];

            foreach ($preferences as $preference)
                $array[$only_for] = array_merge($array[$only_for], $preference->toArray());
        }

        if (count($array) === 1)
            $array = array_values($array)[0];

        return response()->json($array);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $user_id = null) {
		$user = $this->getUser($request, $user_id);
        $inputs = $request->input();

        // On ajoute l'id associé
        if ($request->input('only_for') === 'asso') {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-preferences-asso'))
                abort(403, 'Vous n\'avez pas les droits sur les préférences de l\'association');

            $token = $request->user() ? $request->user()->token() : $request->token();
            $inputs['only_for'] .= '_'.$token->client->asso_id;
        }
        else if ($request->input('only_for') === 'client') {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-preferences-client'))
                abort(403, 'Vous n\'avez pas les droits sur les préférences du client');

            $token = $request->user() ? $request->user()->token() : $request->token();
            $inputs['only_for'] .= '_'.$token->client->id;
        }
        else if ($request->input('only_for', 'global') === 'global') {
            if (!\Scopes::has($request, 'user-'.$verb.'-info-preferences-global'))
                abort(403, 'Vous n\'avez pas les droits sur les préférences globale de l\'utilisateur');
        }
        else
            abort(400, 'only_for peut seulement être asso, client ou global');

		if (\Scopes::isUserToken($request)) {
			$preference = UserPreference::create(array_merge(
				$inputs,
				['user_id' => $user->id]
			));

			return response()->json($preference, 201);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $user_id, $key = null) {
        if (is_null($key))
            list($user_id, $key) = [$key, $user_id];

        $user = $this->getUser($request, $user_id);
		$preference = $this->getPreferences($request, $user, 'get')->key($key);

		if ($preference)
			return response()->json($preference->toArray());
		else
			abort(404, 'Cette personne ne possède pas cette préférence');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id, $key = null) {
        if (is_null($key))
            list($user_id, $key) = [$key, $user_id];

		$user = $this->getUser($request, $user_id);

		if (\Scopes::isUserToken($request)) {
            try {
                $preference = $this->getPreferences($request, $user, 'edit')->key($key);
                $preference->value = $request->input('value', $preference->value);

                if ($preference->update())
                    return response()->json($preference);
                else
                    abort(503, 'Erreur lors de la modification');
            }
            catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce préférence, ou il ne peut être modifié');
            }
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $user_id, $key = null) {
        if (is_null($key))
            list($user_id, $key) = [$key, $user_id];

		$user = $this->getUser($request, $user_id);

		if (\Scopes::isUserToken($request)) {
            $preference = $this->getPreferences($request, $user, 'manage')->key($key);

            try {

				if ($preference->delete())
                    abort(204);
				else
					abort(503, 'Erreur lors de la suppression');
            }
            catch (PortailException $e) {
                abort(404, 'Cette personne ne possède pas ce détail, ou il ne peut être supprimé');
            }
		}
    }
}

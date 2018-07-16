<?php

namespace App\Http\Controllers\v1\Contact;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Visibility;
use App\Traits\HasVisibility;
use App\Interfaces\CanHaveCalendars;

class ContactController extends Controller
{
	use HasVisibility;

	/**
	 * Scopes Group
	 *
	 * Les Scopes requis pour manipuler les Contacts
	 */
	public function __construct() {
		$this->middleware(
			\Scopes::matchOne(
				\Scopes::getDeepestChildren('user-get-contacts'),
				\Scopes::getDeepestChildren('client-get-contacts')
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				\Scopes::getDeepestChildren('user-create-contacts'),
				\Scopes::getDeepestChildren('client-create-contacts')
			),
			['only' => ['store']]
		);
		$this->middleware(
			\Scopes::matchOne(
				\Scopes::getDeepestChildren('user-set-contacts'),
				\Scopes::getDeepestChildren('client-set-contacts')
			),
			['only' => ['update']]
		);
		$this->middleware(
			\Scopes::matchOne(
				\Scopes::getDeepestChildren('user-manage-contacts'),
				\Scopes::getDeepestChildren('client-manage-contacts')
			),
			['only' => ['destroy']]
		);
	}

	protected function checkTokenRights(Request $request, string $verb = 'get') {
		if (!\Scopes::hasOne($request, \Scopes::getTokenType($request).'-get-contacts-'.\ModelResolver::getCategory($request->resource)))
			abort(503, 'L\'application n\'a pas le droit de voir les contacts de cette ressource');
	}

	public function getContact(Request $request, string $verb = 'get') {
		$this->checkTokenRights($request, $verb);
		$contact = $request->resource->contacts()->where('id', $request->contact)->first();

		if ($contact) {
			if (\Auth::id()) {
				if (!$this->isVisible($contact, \Auth::id()))
					abort(503, 'Vous n\'avez pas le droit de voir ce contact');

				if ($verb !== 'get' && !$request->resource->isContactManageableBy(\Auth::id()))
					abort(503, 'Il n\'est pas possible à l\'utilisateur de gérer un contact pour cette ressource');
			}

			return $contact;
		}
		else
			abort(404, "Ce contact n'existe pas pour cette ressource");
	}

	public function isPrivate($user_id, $model = null) {
		if ($model === null)
			return false;

		// Si c'est privée on vérifie si on a le droit d'accès
		return $model->owned_by->isContactAccessibleBy($user_id);
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @param ContactRequest $request
	 * @return JsonResponse
	 */
	public function index(ContactRequest $request): JsonResponse {
		$this->checkTokenRights($request);
		$contacts = $this->hide($request->resource->contacts);

		return response()->json($contacts, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param ContactRequest $request
	 * @return JsonResponse
	 */
	public function store(ContactRequest $request): JsonResponse {
		$this->checkTokenRights($request, 'create');

		$contact = Contact::create($request->input());

		if ($contact) {
			$contact->changeOwnerTo($request->resource)->save();

			return response()->json(Contact::find($contact->id), 201);
		}
		else
			abort(500, "Impossible de créer le contact");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param ContactRequest $request
	 * @return JsonResponse
	 */
	public function show(ContactRequest $request): JsonResponse {
		$contact = $this->getContact($request);

		return response()->json($contact, 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param ContactRequest $request
	 * @return JsonResponse
	 */
	public function update(ContactRequest $request): JsonResponse {
		$contact = $this->getContact($request, 'set');

		if ($contact->update($request->input()))
			return response()->json(Contact::find($contact->id), 201);
		else
			abort(500, "Impossible de modifier le contact");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param ContactRequest $request
	 * @return JsonResponse
	 */
	public function destroy(ContactRequest $request): JsonResponse {
		$contact = $this->getContact($request, 'manage');

		if ($contact->delete())
			abort(204);
		else
			abort(500, "Impossible de supprimer le contact");
	}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
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
				['user-get-info'],
				['client-get-users', 'client-get-assos']
			),
			['only' => ['index', 'show']]
		);
		$this->middleware(
			\Scopes::matchOne(
				['user-get-info'],
				['client-manage-users', 'client-manage-assos']
			),
			['only' => ['store', 'update', 'destroy']]
		);
	}

	public function getContact(Request $request) {
		$contact = $request->resource->contacts()->where('id', $request->contact)->first();

		if ($contact) {
			if (\Auth::id() && !$this->isVisible($contact, \Auth::id()))
				abort(503, 'Vous n\'avez pas le droit de voir ce contact');

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
		if (\Auth::id() && !$request->resource->isContactManageableBy(\Auth::id()))
			abort(503, 'Il n\'est pas possible à l\'utilisateur de créer un contact pour cette ressource');

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
		$contact = $this->getContact($request);

		if (\Auth::id() && !$contact->owned_by->isContactManageableBy(\Auth::id()))
			abort(503, 'Il n\'est pas possible à l\'utilisateur de modifier le contact pour cette ressource');

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
		$contact = $this->getContact($request);

		if (\Auth::id() && !$contact->owned_by->isContactManageableBy(\Auth::id()))
			abort(503, 'Il n\'est pas possible à l\'utilisateur de modifier le contact pour cette ressource');

		if ($contact->delete())
			abort(204);
		else
			abort(500, "Impossible de supprimer le contact");
	}
}

<?php
/**
 * Gère les contacts d'une ressource.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Http\Controllers\v1\Contact;

use App\Http\Controllers\v1\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Visibility;
use App\Interfaces\CanHaveCalendars;
use App\Traits\Controller\v1\HasContacts;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    use HasContacts;

    /**
     * Nécessité de pouvoir gérer les contacts.
     */
    public function __construct()
    {
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

    /**
     * Liste des contacts d'une ressource.
     *
     * @param ContactRequest $request
     * @return JsonResponse
     */
    public function index(ContactRequest $request): JsonResponse
    {
        $this->checkTokenRights($request);
        $contacts = $request->resource->contacts()->getSelection()->map(function ($contact) {
            return $contact->hideData();
        });

        return response()->json($contacts, 200);
    }

    /**
     * Créer un contact pour une ressource.
     *
     * @param ContactRequest $request
     * @return JsonResponse
     */
    public function store(ContactRequest $request): JsonResponse
    {
        $this->checkTokenRights($request, 'create');
        $contact = Contact::create($request->input());

        $contact->changeOwnerTo($request->resource)->save();

        return response()->json(Contact::find($contact->id), 201);
    }

    /**
     * Montre un contact d'une ressource.
     *
     * @param ContactRequest $request
     * @return JsonResponse
     */
    public function show(ContactRequest $request): JsonResponse
    {
        $contact = $this->getContact($request);

        return response()->json($contact, 200);
    }

    /**
     * Met à jour un contact d'une ressource.
     *
     * @param ContactRequest $request
     * @return JsonResponse
     */
    public function update(ContactRequest $request): JsonResponse
    {
        $contact = $this->getContact($request, 'set');

        if ($contact->update($request->input())) {
            return response()->json(Contact::find($contact->id), 201);
        } else {
            abort(500, "Impossible de modifier le contact");
        }
    }

    /**
     * Supprime un contact pour une ressource.
     *
     * @param ContactRequest $request
     * @return void
     */
    public function destroy(ContactRequest $request): void
    {
        $contact = $this->getContact($request, 'manage');

        if ($contact->delete()) {
            abort(204);
        } else {
            abort(500, "Impossible de supprimer le contact");
        }
    }
}

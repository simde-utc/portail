<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\ContactType;
use App\Models\Asso;
use App\Models\User;
use App\Exceptions\PortailException;

class ContactController extends Controller
{
    /* TODO(Natan) :  
        - finir les scopes
        - executer les regex quand on store, update
        - gerer les visibilités
    */

    /**
     * Scopes Group
     *
     * Les Scopes requis pour manipuler les Contacts
     */
    // public function __construct() {
    //     $this->middleware(
    //         \Scopes::matchOne(
    //             ['user-get-identity']
    //         ),
    //         ['only' => ['index', 'show']]
    //     );
    //     $this->middleware(
    //         \Scopes::matchOne(
    //             ['user-manage-identity']
    //         ),
    //         ['only' => ['store', 'update', 'destroy']]
    //     );
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ContactRequest $request)
    {
        $model = $request->resource;

        $contacts = $model->contact;

        return response()->json($contacts, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactRequest $request)
    {
        $canCreate = false;

        if ($request->model == User::class) {
            $canCreate = true;
        }
        else if ($request->model == Asso::class) {
            $asso = $request->resource; 
            $canCreate = ($asso->hasOneRole('resp communication', ['user_id' => \Auth::id()]) || \Auth::user()->hasOneRole('admin'));
        }
        else {
            $canCreate = false;
        }

        if ($canCreate) {
            $contact = new Contact;
            $contact->body = $request->body;
            $contact->description = $request->description;
            $contact->contact_type_id = $request->contact_type_id;
            $contact->visibility_id = $request->visibility_id;
            $contact->contactable_id = $request->resource_id;
            $contact->contactable_type = $request->model;

            if ($contact->save()) {
                $contact = Contact::with([
                    'type',
                ])->find($contact->id);

                return response()->json($contact, 201);
            } else
                abort(500, "Impossible de créer le contact");
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ContactRequest $request)
    {
        $contact = $request->resource->contact()->where('id', $request->contact)->first();

        if ($contact) {
            return response()->json($contact, 200);
        }
        else
            abort(404, "Ce contact n'existe pas pour cette ressource.");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContactRequest $request)
    {
        $contact = $request->resource->contact()->where('id', $request->contact)->first();

        if ($contact && $this->canModify()) {
            if ($request->has('body'))
                $contact->body = $request->body;

            if ($request->has('description'))
                $contact->description = $request->description;

            if ($request->has('contact_type_id'))
                $contact->contact_type_id = $request->contact_type_id;

            if ($request->has('visibility_id'))
                $contact->visibility_id = $request->visibility_id;

            if ($contact->save()) {
                return response()->json($contact, 200);
            } 
            else
                abort(500, "Impossible de modifier le groupe");
        }
        else
            abort(404, "Ce contact n'existe pas pour cette ressource.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactRequest $request)
    {
        $contact = $request->resource->contact()->where('id', $request->contact)->first();

        if ($contact && $this->canModify($contact)) {
            if ($contact->delete())
                return response()->json(["message" => "Contact supprimé."], 204);
            else
                abort(500, "Impossible de supprimer le contact.");
        }
        else
            abort(404, "Ce contact n'existe pas pour cette ressource.");
    }

    protected function canModify($contact) {
        // Si le contact est celui d'un utilisateur, on check si contactable_id == Auth::user()->id.
        if ($contact->contactable_type == User::class) {
            return $contact->contactable_id == Auth::user()->id;
        }
        // Sinon, c'est celui d'une asso, on check si Auth::user() à le droit de modifier les infos de contact.
        else if ($contact->contactable_type == Asso::class) { 
            $resource = $contact->contactable;
            return ($resource->hasOneRole('resp communication', ['user_id' => \Auth::id()]) || \Auth::user()->hasOneRole('admin'));
        }
        // On pourrait également gérer d'autres types de ressources.
        else {
            return false;
        }   
    }
}

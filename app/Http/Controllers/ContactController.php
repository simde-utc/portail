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
    /**
     * Scopes Group
     *
     * Les Scopes requis pour manipuler les Contacts
     */
    // public function __construct() {
    //     $this->middleware(
    //         \Scopes::matchOne(
    //             ['user-get-groups-enabled', 'user-get-groups-disabled'],
    //             ['client-get-groups-enabled', 'client-get-groups-disabled']
    //         ),
    //         ['only' => ['index', 'show']]
    //     );
    //     $this->middleware(
    //         \Scopes::matchOne(
    //             ['user-manage-groups']
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
        $model = $request->model::find($request->id);

        $contacts = $model->contact()->with([
            'type',
        ])->get();

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
        $contact = new Contact;
        $contact->body = $request->body;
        $contact->description = $request->description;
        $contact->contact_type_id = $request->contact_type_id;
        $contact->visibility_id = $request->visibility_id;
        $contact->contactable_id = $request->id;
        $contact->contactable_type = $request->model;

        if ($contact->save()) {
            $contact = $contact->with([
                'type',
            ])->get();

            return response()->json($contact, 201);
        } else {
            return response()->json(["message" => "Impossible de créer le contact"], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ContactRequest $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactRequest $request)
    {
        //
    }
}

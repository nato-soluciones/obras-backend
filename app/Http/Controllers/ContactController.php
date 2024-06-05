<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Get all contacts
     *
     * @return Response
     */
    public function index(): Response
    {
        $contacts = Contact::all();
        return response($contacts, 200);
    }

    /**
     * Create an contact
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $contact = Contact::create($request->all());
        return response($contact, 201);
    }

    /**
     * Get an contact by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $contact = Contact::find($id);
        return response($contact, 200);
    }

    /**
     * Update an contact by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $contact = Contact::find($id);
        $contact->update($request->all());
        return response($contact, 200);
    }

    /**
     * Delete an contact by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
        return response(['message' => 'Contacto eliminado correctamente.'], 200);
    }
}

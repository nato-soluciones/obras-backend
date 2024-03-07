<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Get all notes
     *
     * @return Response
     */
    public function index(): Response
    {
        // return all notes created by the authenticated user
        $notes = Note::where('created_by', auth()->user()->id)->get();
        return response($notes, 200);
    }

    /**
     * Create an note
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $request->merge(['created_by' => auth()->user()->id]);

        $note = Note::create($request->all());

        return response($note, 201);
    }

    /**
     * Get an note by id
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $note = Note::find($id);
        return response($note, 200);
    }

    /**
     * Update an note by id
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $note = Note::find($id);
        $note->update($request->all());

        return response($note, 200);
    }

    /**
     * Delete an note by id
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $note = Note::find($id);
        $note->delete();

        return response(['message' => 'Note deleted'], 204);
    }
}

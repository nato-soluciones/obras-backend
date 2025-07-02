<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expire_at' => 'required|date',
            'color' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }
}

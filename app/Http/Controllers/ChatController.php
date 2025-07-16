<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatRequest;
use App\Http\Services\ChatService;

class ChatController extends Controller
{
    public function __construct(protected ChatService $chatService){}
    

    public function message(ChatRequest $request)
    {
        $response = $this->chatService->message($request->getMessage());
        return response()->json($response);
    }
}

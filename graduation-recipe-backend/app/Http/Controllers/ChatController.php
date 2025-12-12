<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ChatController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $userMessage = $request->message;

        // For now, we will respond with a static echo
        $botResponse = "You said: " . $userMessage;

        return response()->json([
            "status" => "success",
            "message" => $botResponse
        ]);
    }
}

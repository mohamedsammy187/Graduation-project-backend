<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LLMController extends Controller
{
    public function ask(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type'  => 'application/json',
            ])->post(env('OPENAI_API_URL'), [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $request->input('prompt')]
                ],
            ]);

            return response()->json($response->json(), $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'LLM request failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}

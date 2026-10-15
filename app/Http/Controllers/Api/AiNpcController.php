<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Http;

class AiNpcController extends Controller implements HasMiddleware
{
    /**
     * Mendefinisikan middleware menggunakan pendekatan terbaru Laravel
     */
    public static function middleware(): array
    {
        return [
            new Middleware('throttle:5,1'), // Hanya 5 request per menit
        ];
    }

    /**
     * Berinteraksi dengan NPC menggunakan Laravel AI SDK.
     */
    public function interact(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'npc_name' => 'nullable|string',
        ]);

        $npcName = $request->input('npc_name', 'Penjaga Desa');
        $playerMessage = $request->input('message');

        // Menggunakan Laravel HTTP Client untuk memanggil Gemini API
        $apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));

        if (! $apiKey) {
            return response()->json(['error' => 'API Key Gemini belum disetting'], 500);
        }

        $prompt = "Kamu adalah NPC dalam game bernama {$npcName}. Balaslah dengan singkat (1-2 kalimat) dan menggunakan bahasa fantasi/kerajaan. Pemain berkata: '{$playerMessage}'";

        $response = Http::post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key='.$apiKey,
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]
        );

        if ($response->successful()) {
            $reply = $response->json('candidates.0.content.parts.0.text', '...');
        } else {
            $reply = 'Maaf, aku sedang tidak bisa bicara. (Error: '.$response->status().')';
        }

        return response()->json([
            'npc_name' => $npcName,
            'reply' => trim($reply),
        ]);
    }
}

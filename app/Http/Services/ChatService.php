<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatService
{
    /**
     * Send message to chatbot webhook and return response
     */
    public function message(string $message): array
    {
        try {
            $userId = Auth::id();
            $webhookUrl = config('services.chatbot.webhook_url');

            if (!$webhookUrl) {
                throw new Exception('Webhook URL not configured');
            }

            $payload = [
                'id' => (string) $userId,
                'mensaje' => $message
            ];

            $response = Http::timeout(30)->post($webhookUrl, $payload);

            if (!$response->successful()) {
                Log::error('Chatbot webhook failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new Exception('Failed to get chatbot response');
            }

            $responseData = $response->json();

            return $this->formatResponse($responseData);
        } catch (Exception $e) {
            Log::error('ChatService error', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return [
                [
                    'message' => 'Lo siento, hubo un error al procesar tu consulta. Inténtalo nuevamente.'
                ]
            ];
        }
    }

    /**
     * Format webhook response to expected format
     */
    private function formatResponse($responseData): array
    {
        // Handle Make.com response format: {"id": "002", "mensaje": "content..."}
        if (is_array($responseData) && isset($responseData['mensaje'])) {
            return [
                [
                    'message' => $responseData['mensaje']
                ]
            ];
        }

        // Fallback for other string responses
        if (is_string($responseData)) {
            return [
                [
                    'message' => $responseData
                ]
            ];
        }

        // Log unexpected format for debugging
        Log::warning('Unexpected chatbot response format', [
            'response' => $responseData
        ]);

        return [
            [
                'message' => 'Lo siento, no pude procesar la respuesta del chatbot correctamente.'
            ]
        ];
    }
}

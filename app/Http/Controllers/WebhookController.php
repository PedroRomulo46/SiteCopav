<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negociacao;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function stripe(Request $request)
    {
        // Lê diretamente o conteúdo enviado pela Stripe
        $payload = $request->all();

        Log::info('Webhook Stripe recebido:', [
            'type' => $payload['type'] ?? 'desconhecido'
        ]);

        $eventType = $payload['type'] ?? '';

        // Aceita eventos de checkout concluído ou pagamento bem-sucedido
        if (in_array($eventType, ['checkout.session.completed', 'payment_intent.succeeded'])) {
            $object = $payload['data']['object'] ?? [];
            
            // Busca o ID da negociação nos metadados de forma flexível
            $negociacaoId = $object['metadata']['negociacao_id'] 
                ?? $object['payment_intent_data']['metadata']['negociacao_id'] 
                ?? null;

            if ($negociacaoId) {
                $negociacao = Negociacao::find($negociacaoId);

                if ($negociacao) {
                    if ($negociacao->status !== 'pago') {
                        $negociacao->update(['status' => 'pago']);
                        Log::info("Sucesso absoluto: Negociação ID {$negociacaoId} atualizada para 'pago'.");
                    }
                } else {
                    Log::warning("Webhook Stripe: Negociação ID {$negociacaoId} não foi encontrada no banco.");
                }
            } else {
                Log::warning("Webhook Stripe: 'negociacao_id' não encontrado nos metadados.", $object);
            }
        }

        // Retorna sempre 200 OK para a Stripe marcar como verde instantaneamente
        return response()->json(['status' => 'success'], 200);
    }
}
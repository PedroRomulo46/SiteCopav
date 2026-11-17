<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negociacao;

class WebhookController extends Controller
{
    public function stripe(Request $request)
    {
        $payload = $request->all();

        // Verifica se o evento enviado pelo Stripe é a conclusão do checkout
        if (($payload['type'] ?? '') === 'checkout.session.completed') {
            $session = $payload['data']['object'] ?? [];
            $negociacaoId = $session['metadata']['negociacao_id'] ?? null;

            if ($negociacaoId) {
                $negociacao = Negociacao::find($negociacaoId);

                if ($negociacao && $negociacao->status !== 'pago') {
                    // Atualiza o status da negociação para pago no banco de dados
                    $negociacao->update(['status' => 'pago']);

                    // Se você tiver um evento do Reverb para atualizar em tempo real, pode disparar aqui:
                    // event(new \App\Events\PagamentoAprovado($negociacao));
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negociacao;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PagamentoController extends Controller
{
    public function show(Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'propostas.usuario',
        ]);

        $propostaAceita = $negociacao->propostas
            ->first(function ($proposta) {
                return $proposta->status === 'aceita'
                    && (int) $proposta->usuario_id === (int) auth()->id();
            });

        abort_unless($propostaAceita, 403);

        $oferta = $negociacao->oferta;
        $valorUnitario = (float) $propostaAceita->valor;
        $quantidade = (float) $propostaAceita->quantidade;
        $valorTotal = $valorUnitario * $quantidade;

        return view('pagamentos.show', compact(
            'negociacao',
            'propostaAceita',
            'oferta',
            'valorUnitario',
            'quantidade',
            'valorTotal'
        ));
    }

    public function confirmar(Request $request, Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'propostas.usuario',
        ]);

        $propostaAceita = $negociacao->propostas->first(function ($proposta) {
            return $proposta->status === 'aceita'
                && (int) $proposta->usuario_id === (int) auth()->id();
        });

        abort_unless($propostaAceita, 403);

        $valorUnitario = (float) $propostaAceita->valor;
        $quantidade = (float) $propostaAceita->quantidade;
        $valorTotal = $valorUnitario * $quantidade;
        $valorEmCentavos = (int) round($valorTotal * 100);

        // Configura a chave do Stripe que você colocou no .env
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => $negociacao->oferta->produto->nome,
                    ],
                    'unit_amount' => $valorEmCentavos,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'negociacao_id' => (string) $negociacao->id,
            ],
            // Criamos uma rota dedicada ou passamos o ID para garantir a atualização imediata no retorno
            'success_url' => route('pagamentos.sucesso', $negociacao) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pagamentos.show', $negociacao),
        ]);

        // Redireciona para o checkout oficial do Stripe
        return redirect()->away($session->url);
    }

    public function sucesso(Request $request, Negociacao $negociacao)
    {
        // Garante a atualização imediata da negociação para pago ao retornar do Stripe
        if ($negociacao->status !== 'pago' && $negociacao->status !== 'aprovado') {
            $negociacao->update(['status' => 'pago']);
        }

        return redirect()->route('negociacoes.compras')->with('sucesso', 'Pagamento aprovado com sucesso!');
    }
}
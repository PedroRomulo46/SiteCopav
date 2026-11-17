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
            'success_url' => route('negociacoes.compras') . '?pagamento=sucesso',
            'cancel_url' => route('pagamentos.show', $negociacao),
        ]);

        // Redireciona para o checkout oficial do Stripe
        return redirect()->away($session->url);
    }
}
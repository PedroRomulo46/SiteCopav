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

        Stripe::setApiKey(config('services.stripe.secret') ?: env('STRIPE_SECRET'));

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
            'success_url' => route('pagamentos.sucesso', $negociacao) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pagamentos.show', $negociacao),
        ]);

        return redirect()->away($session->url);
    }

    public function sucesso(Request $request, Negociacao $negociacao)
    {
        $usuario = $request->user();

        abort_unless(
            $usuario?->isAdmin() || (int) $negociacao->cliente_id === (int) $usuario?->id,
            403
        );

        if ($negociacao->status !== 'pago' && $negociacao->status !== 'aprovado') {
            $negociacao->update(['status' => 'pago']);
        }

        return redirect()->route('negociacoes.compras')->with('sucesso', 'Pagamento aprovado com sucesso!');
    }
}
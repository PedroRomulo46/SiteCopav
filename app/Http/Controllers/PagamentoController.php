<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negociacao;

class PagamentoController extends Controller
{
    public function show(Negociacao $negociacao)
    {
        // Carrega os dados necessários para a tela.
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'propostas.usuario',
        ]);

        // Recupera a proposta aceita que pertence ao usuário logado.
        $propostaAceita = $negociacao->propostas
            ->first(function ($proposta) {
                return $proposta->status === 'aceita'
                    && (int) $proposta->usuario_id === (int) auth()->id();
            });

        // Somente quem enviou a proposta aceita pode acessar o pagamento.
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

        // Atualiza o status da negociação para pago no banco
        $negociacao->update(['status' => 'pago']);

        $oferta = $negociacao->oferta;
        $valorUnitario = (float) $propostaAceita->valor;
        $quantidade = (float) $propostaAceita->quantidade;
        $valorTotal = $valorUnitario * $quantidade;

        // Retorna a view de sucesso/confirmação do pagamento
        return view('pagamentos.confirmar', compact(
            'negociacao',
            'propostaAceita',
            'oferta',
            'valorTotal'
        ));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use Illuminate\Http\Request;

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
}
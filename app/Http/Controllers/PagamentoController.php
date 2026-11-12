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
        // 1. Validação de segurança idêntica ao método show
        $negociacao->load('propostas');
        
        $propostaAceita = $negociacao->propostas->first(function ($proposta) {
            return $proposta->status === 'aceita'
                && (int) $proposta->usuario_id === (int) auth()->id();
        });

        abort_unless($propostaAceita, 403);

        // 2. Atualiza o status para aprovado/pago no banco de dados
        // Exemplo (ajuste conforme os campos da sua tabela):
        $negociacao->update(['status' => 'pago']);

        // 3. Redireciona de volta com uma mensagem de sucesso
        return redirect()->route('pagamentos.show', $negociacao)
            ->with('sucesso', 'Pagamento simulado com sucesso!');
    }

    public function simularPagamento(Request $request, Negociacao $negociacao)
    {
        // Garante a mesma segurança para a ação de pagar
        $negociacao->load('propostas');
        
        $propostaAceita = $negociacao->propostas->first(function ($proposta) {
            return $proposta->status === 'aceita'
                && (int) $proposta->usuario_id === (int) auth()->id();
        });

        abort_unless($propostaAceita, 403);

        $negociacao->update(['status' => 'pago']);

        // Redireciona de volta com uma mensagem de sucesso
        return redirect()->route('negociacoes.show', $negociacao->id)
            ->with('sucesso', 'Pagamento simulado com sucesso! Transação aprovada.');
    }
}
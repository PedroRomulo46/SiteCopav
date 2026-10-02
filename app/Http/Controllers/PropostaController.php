<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use App\Models\Proposta;
use Illuminate\Http\Request;

class PropostaController extends Controller
{
    public function create(Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente'
        ]);

        $usuario = auth()->user();

        $participa = (
            $usuario->id === $negociacao->cliente_id ||
            $usuario->id === $negociacao->oferta->fornecedor->user_id
        );

        if (!$participa) {
            abort(403);
        }

        return view(
            'propostas.create',
            compact('negociacao')
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'negociacao_id' => 'required|exists:negociacoes,id',
            'valor' => 'required|numeric|min:0',
            'quantidade' => 'nullable|numeric|min:0',
            'observacao' => 'nullable|string',
        ]);

        $negociacao = Negociacao::with('oferta.fornecedor')
            ->findOrFail($dados['negociacao_id']);

        $usuario = $request->user();

        $participa = (
            $usuario->id === $negociacao->cliente_id ||
            $usuario->id === $negociacao->oferta->fornecedor->user_id
        );

        if (!$participa) {
            abort(403);
        }

        $dados['usuario_id'] = $usuario->id;
        $dados['status'] = 'pendente';

        Proposta::create($dados);

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with(
                'sucesso',
                'Proposta enviada com sucesso!'
            );
    }

    public function aceitar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor'
        ]);

        $usuario = auth()->user();

        // Apenas o fornecedor pode aceitar a proposta
        if (
            !$proposta->negociacao->oferta->fornecedor ||
            $proposta->negociacao->oferta->fornecedor->user_id !== $usuario->id
        ) {
            abort(403);
        }

        // Só pode aceitar uma proposta pendente
        if ($proposta->status !== 'pendente') {
            return back()->with(
                'erro',
                'Esta proposta já foi processada.'
            );
        }

        $proposta->update([
            'status' => 'aceita'
        ]);

        $proposta->negociacao->update([
            'status' => 'aceita'
        ]);

        return redirect()
            ->route('negociacoes.show', $proposta->negociacao)
            ->with(
                'sucesso',
                'Proposta aceita com sucesso!'
            );
    }

    public function recusar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor'
        ]);

        $usuario = auth()->user();

        // Apenas o fornecedor pode recusar a proposta
        if (
            !$proposta->negociacao->oferta->fornecedor ||
            $proposta->negociacao->oferta->fornecedor->user_id !== $usuario->id
        ) {
            abort(403);
        }

        // Só pode recusar uma proposta pendente
        if ($proposta->status !== 'pendente') {
            return back()->with(
                'erro',
                'Esta proposta já foi processada.'
            );
        }

        $proposta->update([
            'status' => 'recusada'
        ]);

        return redirect()
            ->route('negociacoes.show', $proposta->negociacao)
            ->with(
                'sucesso',
                'Proposta recusada.'
            );
    }
}
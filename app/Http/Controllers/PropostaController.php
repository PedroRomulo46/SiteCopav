<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use App\Models\Proposta;
use App\Models\User;
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

        // Verifica se o usuário logado participa desta negociação
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

        // Verifica se o usuário participa da negociação
        $participa = (
            $usuario->id === $negociacao->cliente_id ||
            $usuario->id === $negociacao->oferta->fornecedor->user_id
        );

        if (!$participa) {
            abort(403);
        }

        // O usuário da proposta vem do login
        $dados['usuario_id'] = $usuario->id;

        // Toda proposta nova começa como pendente
        $dados['status'] = 'pendente';

        Proposta::create($dados);

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with(
                'sucesso',
                'Proposta enviada com sucesso!'
            );
    }
} 
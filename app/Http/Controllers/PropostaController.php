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

        $usuarios = User::whereIn('id', [
            $negociacao->cliente_id,
            $negociacao->oferta->fornecedor->user_id
        ])->get();

        return view(
            'propostas.create',
            compact('negociacao', 'usuarios')
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'negociacao_id' => 'required|exists:negociacoes,id',
            'usuario_id' => 'required|exists:users,id',
            'valor' => 'required|numeric|min:0',
            'quantidade' => 'nullable|numeric|min:0',
            'observacao' => 'nullable|string',
            'status' => 'required|in:pendente,aceita,recusada',
        ]);

        Proposta::create($dados);

        return redirect()
            ->route(
                'negociacoes.show',
                $dados['negociacao_id']
            )
            ->with(
                'sucesso',
                'Proposta enviada com sucesso!'
            );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use App\Models\Proposta;
use Illuminate\Http\Request;
use App\Events\PropostaCriada;

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

        // O dono da oferta não pode fazer proposta na própria oferta
        if (
            $negociacao->oferta->fornecedor &&
            $negociacao->oferta->fornecedor->user_id === $usuario->id
        ) {
            abort(403);
        }

        return view('propostas.create', compact('negociacao'));
    }

    

    public function store(Request $request)
    {
        $dados = $request->validate([
            'negociacao_id' => 'required|exists:negociacoes,id',
            'valor' => 'required|numeric|min:0',
            'quantidade' => 'required|numeric|min:0.01',
            'observacao' => 'nullable|string',
        ]);

        $negociacao = Negociacao::with([
            'oferta.fornecedor'
        ])->findOrFail($dados['negociacao_id']);

        $usuario = $request->user();

        // Dono da oferta não pode fazer proposta
        if (
            $negociacao->oferta->fornecedor &&
            $negociacao->oferta->fornecedor->user_id === $usuario->id
        ) {
            abort(403);
        }

        // Garante que o usuário realmente participa da negociação
        if ($negociacao->cliente_id !== $usuario->id) {
            abort(403);
        }

        $dados['usuario_id'] = $usuario->id;
        $dados['status'] = 'pendente';

        $proposta = Proposta::create($dados);

        $proposta->load([
            'negociacao.oferta.produto',
            'negociacao.oferta.fornecedor',
            'usuario',
        ]);

        event(new PropostaCriada($proposta));

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with('sucesso', 'Proposta enviada com sucesso!');
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

    public function visualizar(Proposta $proposta)
    {
        $proposta->load([
            'negociacao.oferta.fornecedor'
        ]);

        $usuario = auth()->user();

        // Apenas o fornecedor dono da oferta ou um administrador
        // pode visualizar a proposta dessa forma.
        if ($usuario->user_type !== 'admin') {
            if (
                !$usuario->fornecedor ||
                !$proposta->negociacao->oferta->fornecedor ||
                $proposta->negociacao->oferta->fornecedor->id !== $usuario->fornecedor->id
            ) {
                abort(403);
            }
        }

        // Marca a proposta como visualizada
        if (!$proposta->visualizada_em) {
            $proposta->update([
                'visualizada_em' => now(),
            ]);
        }

        // Entra na negociação daquela proposta
        return redirect()->route(
            'negociacoes.show',
            $proposta->negociacao_id
        );
    }
}
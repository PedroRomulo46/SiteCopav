<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;
use App\Models\Oferta;
use App\Models\User;
use Illuminate\Http\Request;

class NegociacaoController extends Controller
{
    public function index()
    {
        $negociacoes = Negociacao::with([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente'
        ])->get();

        return view('negociacoes.index', compact('negociacoes'));
    }

    public function create()
    {
        $ofertas = Oferta::with(['produto', 'fornecedor'])
            ->where('status', 'publicada')
            ->get();

        $clientes = User::where('user_type', 'cliente')->get();

        return view(
            'negociacoes.create',
            compact('ofertas', 'clientes')
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'oferta_id' => 'required|exists:ofertas,id',
            'cliente_id' => 'required|exists:users,id',
            'status' => 'required|in:pendente,em_negociacao,aceita,recusada,concluida,cancelada',
        ]);

        Negociacao::create($dados);

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação criada com sucesso!');
    }

    public function show(Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
            'propostas'
        ]);

        return view('negociacoes.show', compact('negociacao'));
    }

    public function edit(Negociacao $negociacao)
    {
        $ofertas = Oferta::with(['produto', 'fornecedor'])
            ->where('status', 'publicada')
            ->get();

        $clientes = User::where('user_type', 'cliente')->get();

        return view(
            'negociacoes.edit',
            compact('negociacao', 'ofertas', 'clientes')
        );
    }

    public function update(Request $request, Negociacao $negociacao)
    {
        $dados = $request->validate([
            'oferta_id' => 'required|exists:ofertas,id',
            'cliente_id' => 'required|exists:users,id',
            'status' => 'required|in:pendente,em_negociacao,aceita,recusada,concluida,cancelada',
        ]);

        $negociacao->update($dados);

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação atualizada com sucesso!');
    }

    public function destroy(Negociacao $negociacao)
    {
        $negociacao->delete();

        return redirect()
            ->route('negociacoes.index')
            ->with('sucesso', 'Negociação excluída com sucesso!');
    }
}

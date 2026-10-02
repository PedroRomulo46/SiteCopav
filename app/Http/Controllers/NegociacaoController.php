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

        return view(
            'negociacoes.create',
            compact('ofertas')
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'oferta_id' => 'required|exists:ofertas,id',
        ]);

        $oferta = Oferta::where('id', $dados['oferta_id'])
            ->where('status', 'publicada')
            ->first();

        if (!$oferta) {
            abort(403);
        }

        $usuario = $request->user();

        // Impede o fornecedor de negociar a própria oferta
        if (
            $usuario->fornecedor &&
            $oferta->fornecedor_id === $usuario->fornecedor->id
        ) {
            return back()->with(
                'erro',
                'Você não pode negociar sua própria oferta.'
            );
        }

        // Verifica se já existe uma negociação para essa oferta
        $negociacaoExistente = Negociacao::where('oferta_id', $oferta->id)
            ->where('cliente_id', $usuario->id)
            ->first();

        if ($negociacaoExistente) {
            return redirect()
                ->route('negociacoes.show', $negociacaoExistente)
                ->with(
                    'sucesso',
                    'Você já possui uma negociação para esta oferta.'
                );
        }

        $negociacao = Negociacao::create([
            'oferta_id' => $oferta->id,
            'cliente_id' => $usuario->id,
            'status' => 'em_negociacao',
        ]);

        return redirect()
            ->route('negociacoes.show', $negociacao)
            ->with(
                'sucesso',
                'Negociação iniciada com sucesso!'
            );
    }

    public function show(Negociacao $negociacao)
    {
        $negociacao->load([
            'oferta.produto',
            'oferta.fornecedor',
            'cliente',
            'propostas.usuario'
        ]);

        return view('negociacoes.show', compact('negociacao'));
    }

    public function edit(Negociacao $negociacao)
    {
        $ofertas = Oferta::where('status', 'publicada')->get();
        $clientes = User::where('user_type', 'cliente')->get();

        return view('negociacoes.edit', compact('negociacao', 'ofertas', 'clientes'));
    }

    public function update(Request $request, Negociacao $negociacao) {
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
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
        $usuario = auth()->user();

        if ($usuario->user_type === 'fornecedor') {

            $fornecedor = $usuario->fornecedor;

            if (!$fornecedor) {
                abort(403);
            }

            $negociacoes = Negociacao::whereHas('oferta', function ($query) use ($fornecedor) {
                $query->where('fornecedor_id', $fornecedor->id);
            })
            ->with([
                'oferta.produto',
                'oferta.fornecedor',
                'cliente',
                'propostas.usuario'
            ])
            ->get();

        } else {

            $negociacoes = Negociacao::where('cliente_id', $usuario->id)
                ->with([
                    'oferta.produto',
                    'oferta.fornecedor',
                    'cliente',
                    'propostas.usuario'
                ])
                ->get();
        }

        return view(
            'negociacoes.index',
            compact('negociacoes')
        );
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

    // Busca a oferta independente do formato do status
    $oferta = Oferta::find($dados['oferta_id']);

    if (!$oferta) {
        return back()->with('error', 'Oferta não encontrada ou indisponível.');
    }

    $usuario = $request->user();

    // Impede o fornecedor dono do lote de negociar a própria oferta
    if (
        ($usuario->fornecedor && $oferta->fornecedor_id === $usuario->fornecedor->id) ||
        ($usuario->id === $oferta->user_id)
    ) {
        return back()->with('error', 'Você não pode negociar sua própria oferta.');
    }

    // Verifica se já existe uma negociação em aberto para este usuário e oferta
    $negociacaoExistente = Negociacao::where('oferta_id', $oferta->id)
        ->where('cliente_id', $usuario->id)
        ->first();

    if ($negociacaoExistente) {
        return redirect()
            ->route('negociacoes.show', $negociacaoExistente)
            ->with('success', 'Você já possui uma negociação ativa para esta oferta.');
    }

    // Cria a nova negociação
    $negociacao = Negociacao::create([
        'oferta_id'  => $oferta->id,
        'cliente_id' => $usuario->id,
        'status'     => 'em_negociacao',
    ]);

    return redirect()
        ->route('negociacoes.show', $negociacao)
        ->with('success', 'Negociação iniciada com sucesso!');
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
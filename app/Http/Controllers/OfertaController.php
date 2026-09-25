<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\Produto;
use Illuminate\Http\Request;

class OfertaController extends Controller
{
    public function index()
    {
        $ofertas = Oferta::with([
            'fornecedor',
            'produto'
        ])->get();

        return view('ofertas.index', compact('ofertas'));
    }

    public function create()
    {
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            return redirect()
                ->route('fornecedores.create')
                ->with(
                    'sucesso',
                    'Você precisa cadastrar um fornecedor antes de cadastrar ofertas.'
                );
        }

        if ($fornecedor->status !== 'ativo') {
            return redirect()
                ->route('fornecedores.show', $fornecedor)
                ->with(
                    'sucesso',
                    'Seu fornecedor ainda não está ativo.'
                );
        }

        $produtos = Produto::where('fornecedor_id', $fornecedor->id)
            ->with('categoria')
            ->get();

        return view(
            'ofertas.create',
            compact('produtos')
        );
    }

    public function store(Request $request)
    {
        $fornecedor = $request->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if ($fornecedor->status !== 'ativo') {
            abort(403);
        }

        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_validade' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:rascunho,publicada,encerrada,cancelada',
        ]);

        $produto = Produto::where('id', $dados['produto_id'])
            ->where('fornecedor_id', $fornecedor->id)
            ->first();

        if (!$produto) {
            abort(403);
        }

        $dados['fornecedor_id'] = $fornecedor->id;

        Oferta::create($dados);

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta cadastrada com sucesso!');
    }

    public function show(Oferta $oferta)
    {
        $oferta->load([
            'fornecedor',
            'produto'
        ]);

        $produto = $oferta->produto;

        return view(
            'ofertas.show',
            compact('oferta', 'produto')
        );
    }

    public function edit(Oferta $oferta)
    {
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if (
            $oferta->fornecedor_id !== $fornecedor->id &&
            auth()->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $produtos = Produto::where('fornecedor_id', $fornecedor->id)
            ->with('categoria')
            ->get();

        return view(
            'ofertas.edit',
            compact('oferta', 'produtos')
        );
    }

    public function update(Request $request, Oferta $oferta)
    {
        $fornecedor = $request->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if (
            $oferta->fornecedor_id !== $fornecedor->id &&
            $request->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_validade' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:rascunho,publicada,encerrada,cancelada',
        ]);

        $produto = Produto::where('id', $dados['produto_id'])
            ->where('fornecedor_id', $fornecedor->id)
            ->first();

        if (!$produto && $request->user()->user_type !== 'admin') {
            abort(403);
        }

        $oferta->update($dados);

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta atualizada com sucesso!');
    }

    public function destroy(Oferta $oferta)
    {
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if (
            $oferta->fornecedor_id !== $fornecedor->id &&
            auth()->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $oferta->delete();

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta excluída com sucesso!');
    }
}
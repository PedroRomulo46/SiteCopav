<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\Fornecedor;
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

        return view(
            'site.ofertas.index',
            compact('ofertas')
        );
    }

    public function create()
    {
        $fornecedores = Fornecedor::where('status', 'ativo')->get();

        $produtos = Produto::with([
            'fornecedor',
            'categoria'
        ])->get();

        return view(
            'site.ofertas.create',
            compact('fornecedores', 'produtos')
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_validade' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:rascunho,publicada,encerrada,cancelada',
        ]);

        Oferta::create($dados);

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta cadastrada com sucesso!');
    }

    public function show(Oferta $oferta)
    {
        $oferta->load(['fornecedor', 'produto']);

        // Extrair produto associado a oferta
        $produto = $oferta->produto;

        return view(
            'site.details',
            compact('oferta', 'produto') // Passar 'oferta' e 'produto' na view
        );
    }

    public function edit(Oferta $oferta)
    {
        $fornecedores = Fornecedor::where('status', 'ativo')->get();

        $produtos = Produto::with([
            'fornecedor',
            'categoria'
        ])->get();

        return view(
            'site.ofertas.edit',
            compact(
                'oferta',
                'fornecedores',
                'produtos'
            )
        );
    }

    public function update(Request $request, Oferta $oferta)
    {
        $dados = $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'localizacao' => 'nullable|string|max:255',
            'data_inicio' => 'nullable|date',
            'data_validade' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:rascunho,publicada,encerrada,cancelada',
        ]);

        $oferta->update($dados);

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta atualizada com sucesso!');
    }

    public function destroy(Oferta $oferta)
    {
        $oferta->delete();

        return redirect()
            ->route('ofertas.index')
            ->with('sucesso', 'Oferta excluída com sucesso!');
    }
}
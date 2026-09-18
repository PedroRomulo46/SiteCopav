<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Categoria;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index()
    {
        $demandas = Demanda::with([
            'cliente',
            'categoria'
        ])->get();

        return view(
            'site.testes.demandas.index',
            compact('demandas')
        );
    }

    public function create()
    {
        $categorias = Categoria::all();

        return view(
            'site.testes.demandas.create',
            compact('categorias')
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome_produto' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'valor_maximo' => 'nullable|numeric|min:0',
            'localizacao' => 'nullable|string|max:255',
            'data_limite' => 'nullable|date',
        ]);

        $dados['cliente_id'] = auth()->id();
        $dados['status'] = 'aberta';

        Demanda::create($dados);

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda cadastrada com sucesso!');
    }

    public function show(Demanda $demanda)
    {
        $demanda->load([
            'cliente',
            'categoria',
            'ofertasDiretas.fornecedor'
        ]);

        return view(
            'site.testes.demandas.show',
            compact('demanda')
        );
    }

    public function edit(Demanda $demanda)
    {
        $categorias = Categoria::all();

        return view(
            'site.testes.demandas.edit',
            compact('demanda', 'categorias')
        );
    }

    public function update(Request $request, Demanda $demanda)
    {
        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome_produto' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'quantidade' => 'required|numeric|min:0',
            'unidade' => 'required|string|max:50',
            'valor_maximo' => 'nullable|numeric|min:0',
            'localizacao' => 'nullable|string|max:255',
            'data_limite' => 'nullable|date',
            'status' => 'required|in:aberta,encerrada,cancelada',
        ]);

        $demanda->update($dados);

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda atualizada com sucesso!');
    }

    public function destroy(Demanda $demanda)
    {
        $demanda->delete();

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda excluída com sucesso!');
    }
}
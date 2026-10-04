<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Categoria;
use Illuminate\Http\Request;

class DemandaController extends Controller
{
    public function index()
    {
        $demandas = Demanda::with(['cliente', 'categoria'])->latest()->get();

        return view('demandas.index', compact('demandas'));
    }

    public function create()
    {
        $categorias = Categoria::all();

        return view('demandas.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome_produto' => 'required|string|max:255',
            'descricao'    => 'nullable|string',
            'quantidade'   => 'required|numeric|min:0',
            'unidade'      => 'required|string|max:50',
            'valor_maximo' => 'nullable|numeric|min:0',
            'localizacao'  => 'nullable|string|max:255',
            'data_limite'  => 'nullable|date',
        ]);

        // Vincula a demanda ao usuário logado (Empresa/Cooperativa)
        $dados['cliente_id'] = auth()->id();
        $dados['user_id']    = auth()->id(); // Garante compatibilidade caso a tabela use user_id
        $dados['status']     = 'aberta';

        Demanda::create($dados);

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda da empresa publicada com sucesso!');
    }

    public function show(Demanda $demanda)
    {
        $demanda->load(['cliente', 'categoria', 'ofertasDiretas.fornecedor']);

        return view('demandas.show', compact('demanda'));
    }

    public function edit(Demanda $demanda)
    {
        $usuario = auth()->user();

        // Permite se for o criador ou se for Admin/Empresa
        if ($usuario->id !== $demanda->cliente_id && !$usuario->is_admin && $usuario->user_type !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $categorias = Categoria::all();

        return view('demandas.edit', compact('demanda', 'categorias'));
    }

    public function update(Request $request, Demanda $demanda)
    {
        $usuario = auth()->user();

        if ($usuario->id !== $demanda->cliente_id && !$usuario->is_admin && $usuario->user_type !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome_produto' => 'required|string|max:255',
            'descricao'    => 'nullable|string',
            'quantidade'   => 'required|numeric|min:0',
            'unidade'      => 'required|string|max:50',
            'valor_maximo' => 'nullable|numeric|min:0',
            'localizacao'  => 'nullable|string|max:255',
            'data_limite'  => 'nullable|date',
            'status'       => 'required|in:aberta,encerrada,cancelada',
        ]);

        $demanda->update($dados);

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda atualizada com sucesso!');
    }

    public function destroy(Demanda $demanda)
    {
        $usuario = auth()->user();

        if ($usuario->id !== $demanda->cliente_id && !$usuario->is_admin && $usuario->user_type !== 'admin') {
            abort(403, 'Ação não autorizada.');
        }

        $demanda->delete();

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda excluída com sucesso!');
    }
}
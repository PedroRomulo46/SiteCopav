<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Demanda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DemandaController extends Controller
{
    public function index()
    {
        $demandas = Demanda::with(['cliente', 'categoria'])->latest()->get();

        return view('demandas.index', compact('demandas'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->podeGerenciarDemandas(), 403, 'Ação não autorizada.');

        $categorias = Categoria::all();

        return view('demandas.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->podeGerenciarDemandas(), 403, 'Ação não autorizada.');

        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome_produto' => 'required|string|max:255',
            'descricao'    => 'nullable|string',
            'quantidade'   => 'required|numeric|min:0',
            'unidade'      => 'required|string|max:50',
            'valor_maximo' => 'nullable|numeric|min:0',
            'localizacao'  => 'nullable|string|max:255',
            'data_limite'  => 'nullable|date',
            'imagem'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Vincula a demanda ao usuário logado pelo cliente_id
        $dados['cliente_id'] = auth()->id();
        $dados['status']     = 'aberta';

        if ($request->hasFile('imagem')) {
            $dados['imagem'] = $request
                ->file('imagem')
                ->store('demandas', 'public');
        }

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

        $donoOuAdmin = $usuario !== null && (
            $usuario->isAdmin()
            || (int) $usuario->id === (int) $demanda->cliente_id
        );

        if (! $donoOuAdmin) {
            abort(403, 'Ação não autorizada.');
        }

        $categorias = Categoria::all();

        return view('demandas.edit', compact('demanda', 'categorias'));
    }

    public function update(Request $request, Demanda $demanda)
    {
        $usuario = auth()->user();

        $donoOuAdmin = $usuario !== null && (
            $usuario->isAdmin()
            || (int) $usuario->id === (int) $demanda->cliente_id
        );

        if (! $donoOuAdmin) {
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
            'imagem'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('imagem')) {
            if ($demanda->imagem && ! str_contains($demanda->imagem, 'assets/')) {
                Storage::disk('public')->delete(ltrim(str_replace('public/', '', $demanda->imagem), '/'));
            }

            $dados['imagem'] = $request
                ->file('imagem')
                ->store('demandas', 'public');
        }

        $demanda->update($dados);

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda atualizada com sucesso!');
    }

    public function destroy(Demanda $demanda)
    {
        $usuario = auth()->user();

        $donoOuAdmin = $usuario !== null && (
            $usuario->isAdmin()
            || (int) $usuario->id === (int) $demanda->cliente_id
        );

        if (! $donoOuAdmin) {
            abort(403, 'Ação não autorizada.');
        }

        if ($demanda->imagem && ! str_contains($demanda->imagem, 'assets/')) {
            Storage::disk('public')->delete(ltrim(str_replace('public/', '', $demanda->imagem), '/'));
        }

        $demanda->delete();

        return redirect()
            ->route('demandas.index')
            ->with('sucesso', 'Demanda excluída com sucesso!');
    }
}
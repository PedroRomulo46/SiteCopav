<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\CategoriaSugestao;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('produtos')->get();
        $sugestoes = auth()->user()?->isAdmin()
            ? CategoriaSugestao::with('user')->latest()->get()
            : collect();

        return view('categorias.index', compact('categorias', 'sugestoes'));
    }

    public function create()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('categorias.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        Categoria::create($dados);

        return redirect()
            ->route('categorias.index')
            ->with('sucesso', 'Categoria cadastrada com sucesso!');
    }

    public function show(Categoria $categoria)
    {
        $categoria->load([
            'produtos.ofertas' => function ($query) {
                $query->where('status', 'publicada');
            },
            'produtos.fornecedor',
        ]);

        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $categoria->update($dados);

        return redirect()
            ->route('categorias.index')
            ->with('sucesso', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Categoria $categoria)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('sucesso', 'Categoria excluída com sucesso!');
    }

    public function sugerir()
    {
        return view('categorias.sugerir');
    }

    public function armazenarSugestao(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:categorias,nome'],
            'descricao' => ['nullable', 'string'],
        ], [
            'nome.unique' => 'Já existe uma categoria cadastrada com esse nome.',
        ]);

        CategoriaSugestao::create([
            'user_id' => auth()->id(),
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'] ?? null,
        ]);

        return redirect()
            ->route('categorias.sugerir')
            ->with('sucesso', 'Sugestão enviada com sucesso!');
    }

    public function aprovarSugestao(CategoriaSugestao $sugestao)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        Categoria::firstOrCreate(
            ['nome' => $sugestao->nome],
            ['descricao' => $sugestao->descricao]
        );

        $sugestao->delete();

        return redirect()
            ->route('categorias.index')
            ->with('sucesso', 'Sugestão aprovada e categoria cadastrada com sucesso!');
    }

    public function recusarSugestao(CategoriaSugestao $sugestao)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $sugestao->delete();

        return redirect()
            ->route('categorias.index')
            ->with('sucesso', 'Sugestão recusada.');
    }
}
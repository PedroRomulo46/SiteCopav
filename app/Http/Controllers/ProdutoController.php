<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    public function index()
    {
        $produtos = Produto::with([
            'fornecedor',
            'categoria'
        ])->get();

        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            return redirect()
                ->route('fornecedores.create')
                ->with('sucesso', 'Você precisa cadastrar um fornecedor antes de cadastrar produtos.');
        }

        if ($fornecedor->status !== 'ativo') {
            return redirect()
                ->route('fornecedores.show', $fornecedor)
                ->with('sucesso', 'Seu fornecedor ainda não está ativo.');
        }

        $categorias = Categoria::all();

        return view(
            'produtos.create',
            compact('categorias')
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
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade' => 'required|string|max:50',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $dados['fornecedor_id'] = $fornecedor->id;

        if ($request->hasFile('imagem')) {
            $dados['imagem'] = $request
                ->file('imagem')
                ->store('produtos', 'public');
        }

        Produto::create($dados);

        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto cadastrado com sucesso!');
    }

    public function show(Produto $produto)
    {
        $produto->load([
            'fornecedor',
            'categoria'
        ]);

        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        $fornecedores = Fornecedor::where('status', 'ativo')->get();
        $categorias = Categoria::all();

        return view('produtos.edit', compact('produto', 'fornecedores', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade' => 'required|string|max:50',
        ]);

        $produto->update($dados);

        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }
}
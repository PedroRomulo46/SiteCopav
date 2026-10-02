<?php

namespace App\Http\Controllers;

use App\Models\Produto;
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

 public function meusProdutos()
{
    $fornecedor = auth()->user()->fornecedor;

    if (!$fornecedor) {
        abort(403);
    }

    $produtos = Produto::where('fornecedor_id', $fornecedor->id)
        ->with([
            'fornecedor',
            'categoria'
        ])
        ->get();

    dd([
        'fornecedor_logado' => $fornecedor->id,
        'produtos' => $produtos->pluck('id'),
        'fornecedores_dos_produtos' => $produtos->pluck('fornecedor_id'),
    ]);

    return view(
        'produtos.meus',
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
            'categoria',
            'ofertas'
        ]);

        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        $usuario = auth()->user();

        if ($usuario->user_type !== 'admin') {

            $fornecedor = $usuario->fornecedor;

            if (!$fornecedor || $produto->fornecedor_id !== $fornecedor->id) {
                abort(403);
            }
        }

        $categorias = Categoria::all();

        return view(
            'produtos.edit',
            compact('produto', 'categorias')
        );
    }

    public function update(Request $request, Produto $produto)
    {
        $usuario = $request->user();

        // Apenas o admin pode alterar produtos de qualquer fornecedor
        if ($usuario->user_type !== 'admin') {

            $fornecedor = $usuario->fornecedor;

            if (!$fornecedor || $produto->fornecedor_id !== $fornecedor->id) {
                abort(403);
            }
        }

        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'unidade' => 'required|string|max:50',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('imagem')) {

            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }

            $dados['imagem'] = $request
                ->file('imagem')
                ->store('produtos', 'public');
        }

        $produto->update($dados);

        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $usuario = auth()->user();

        // Apenas o admin pode excluir produtos de qualquer fornecedor
        if ($usuario->user_type !== 'admin') {

            $fornecedor = $usuario->fornecedor;

            if (!$fornecedor || $produto->fornecedor_id !== $fornecedor->id) {
                abort(403);
            }
        }

        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }
}
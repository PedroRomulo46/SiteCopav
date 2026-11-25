<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        if ($usuario?->isAdmin()) {
            $produtos = Produto::with([
                'fornecedor',
                'categoria',
            ])
                ->orderBy('nome')
                ->get();
        } else {
            if (! $usuario?->isFornecedor()) {
                abort(403);
            }

            $produtos = Produto::where('fornecedor_id', $usuario->fornecedor->id)
                ->with([
                    'fornecedor',
                    'categoria',
                ])
                ->orderBy('nome')
                ->get();
        }

        return view('produtos.index', compact('produtos'));
    }

    public function meusProdutos()
    {
        $usuario = auth()->user();

        if (! $usuario?->isFornecedor()) {
            abort(403);
        }

        $produtos = Produto::where('fornecedor_id', $usuario->fornecedor->id)
            ->with([
                'fornecedor',
                'categoria',
            ])
            ->get();

        return view('produtos.meus', compact('produtos'));
    }

    public function create()
    {
        $usuario = auth()->user();
        $fornecedor = $usuario?->fornecedor;

        if (! $fornecedor) {
            return redirect()
                ->route('fornecedores.create')
                ->with(
                    'sucesso',
                    'Você precisa cadastrar um fornecedor antes de cadastrar produtos.'
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

        $categorias = Categoria::orderBy('nome')->get();

        return view('produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $usuario = $request->user();
        $fornecedor = $usuario?->fornecedor;

        if (! $usuario?->isFornecedor() || ! $fornecedor || $fornecedor->status !== 'ativo') {
            abort(403);
        }

        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome'         => 'required|string|max:255',
            'descricao'    => 'nullable|string',
            'unidade'      => 'required|string|max:50',
            'imagem'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $dados['fornecedor_id'] = $fornecedor->id;

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $dados['imagem'] = $this->salvarImagemProduto($request->file('imagem'));
        } else {
            unset($dados['imagem']);
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
            'ofertas',
        ]);

        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        $usuario = auth()->user();
        $isAdmin = $usuario?->isAdmin() ?? false;
        $donoDoProduto = $usuario?->isFornecedor() && $produto->fornecedor_id === $usuario->fornecedor->id;

        if (! $isAdmin && ! $donoDoProduto) {
            abort(403);
        }

        $categorias = Categoria::orderBy('nome')->get();

        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $usuario = $request->user();
        $isAdmin = $usuario?->isAdmin() ?? false;
        $donoDoProduto = $usuario?->isFornecedor() && $produto->fornecedor_id === $usuario->fornecedor->id;

        if (! $isAdmin && ! $donoDoProduto) {
            abort(403);
        }

        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome'         => 'required|string|max:255',
            'descricao'    => 'nullable|string',
            'unidade'      => 'required|string|max:50',
            'imagem'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
            $this->removerImagemProduto($produto->imagem);
            $dados['imagem'] = $this->salvarImagemProduto($request->file('imagem'));
        }

        $produto->update($dados);

        return redirect()
            ->route('produtos.show', $produto)
            ->with('sucesso', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $usuario = auth()->user();
        $isAdmin = $usuario?->isAdmin() ?? false;
        $donoDoProduto = $usuario?->isFornecedor() && $produto->fornecedor_id === $usuario->fornecedor->id;

        if (! $isAdmin && ! $donoDoProduto) {
            abort(403);
        }

        $this->removerImagemProduto($produto->imagem);
        $produto->delete();

        return redirect()
            ->route('produtos.index')
            ->with('sucesso', 'Produto excluído com sucesso!');
    }

    private function salvarImagemProduto($file): string
    {
        $diretorio = public_path('storage/produtos');

        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $nomeArquivo = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($diretorio, $nomeArquivo);

        return 'produtos/' . $nomeArquivo;
    }

    private function removerImagemProduto(?string $caminho): void
    {
        if (! $caminho) {
            return;
        }

        $arquivo = public_path('storage/' . ltrim(str_replace('public/', '', $caminho), '/'));

        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
    }
}
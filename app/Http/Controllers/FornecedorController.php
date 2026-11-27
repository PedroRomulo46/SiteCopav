<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();

        return view('fornecedores.index', compact('fornecedores'));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->fornecedor) {
            return redirect()
                ->route('fornecedores.show', $user->fornecedor)
                ->with('sucesso', 'Você já possui um cadastro de fornecedor.');
        }

        return view('fornecedores.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->fornecedor) {
            return redirect()
                ->route('fornecedores.show', $user->fornecedor)
                ->with('sucesso', 'Você já possui um cadastro de fornecedor.');
        }

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:255|unique:fornecedores,documento',
            'telefone' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
        ]);

        $dados['user_id'] = $user->id;
        $dados['status'] = 'ativo';
        $dados['estado'] = strtoupper($dados['estado']);

        Fornecedor::create($dados);

        if (! $user->isAdmin()) {
            $user->update(['user_type' => 'fornecedor']);
        }

        $user->unsetRelation('fornecedor');

        return redirect()
            ->route('ofertas.create')
            ->with('sucesso', 'Fornecedor cadastrado com sucesso! Agora você pode criar sua oferta.');
    }

    public function show(Fornecedor $fornecedor)
    {
        $fornecedor->load([
            'categorias',
            'ofertas' => function ($query) {
                $query->where('status', 'publicada')
                    ->with('produto.categoria')
                    ->latest();
            },
        ]);

        $todasCategorias = Categoria::orderBy('nome')->get();

        $categoriasSelecionadasIds = $fornecedor->categorias
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($categoriasSelecionadasIds)) {
            $categoriasSelecionadasIds = $fornecedor->ofertas
                ->pluck('produto.categoria.id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $principaisOfertas = $fornecedor->ofertas->take(6);

        return view('fornecedores.show', compact(
            'fornecedor',
            'todasCategorias',
            'categoriasSelecionadasIds',
            'principaisOfertas'
        ));
    }

    public function edit(Fornecedor $fornecedor)
    {
        if (
            auth()->id() !== $fornecedor->user_id &&
            ! auth()->user()->isAdmin()
        ) {
            abort(403);
        }

        $fornecedor->load('categorias');
        $categorias = Categoria::orderBy('nome')->get();

        return view('fornecedores.edit', compact('fornecedor', 'categorias'));
    }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        if (
            auth()->id() !== $fornecedor->user_id &&
            ! auth()->user()->isAdmin()
        ) {
            abort(403);
        }

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:255|unique:fornecedores,documento,' . $fornecedor->id,
            'telefone' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'categorias' => 'nullable|array',
            'categorias.*' => 'integer|exists:categorias,id',
        ]);

        $dados['estado'] = strtoupper($dados['estado']);

        $fornecedor->update($dados);
        $fornecedor->categorias()->sync($dados['categorias'] ?? []);

        return redirect()
            ->route('fornecedores.show', $fornecedor)
            ->with('sucesso', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Fornecedor $fornecedor)
    {
        if (
            auth()->id() !== $fornecedor->user_id &&
            ! auth()->user()->isAdmin()
        ) {
            abort(403);
        }

        $fornecedor->delete();

        return redirect()
            ->route('fornecedores.index')
            ->with('sucesso', 'Fornecedor excluído com sucesso!');
    }
}
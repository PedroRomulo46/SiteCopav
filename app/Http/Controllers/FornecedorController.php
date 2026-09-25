<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use App\Models\User;
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
        if (auth()->user()->fornecedor) {
            return redirect()
                ->route('fornecedores.show', auth()->user()->fornecedor)
                ->with('sucesso', 'Você já possui um cadastro de fornecedor.');
        }

        return view('fornecedores.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->fornecedor) {
            return redirect()
                ->route('fornecedores.show', $request->user()->fornecedor)
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

        $dados['user_id'] = $request->user()->id;
        $dados['status'] = 'pendente';

        Fornecedor::create($dados);

        return redirect()
            ->route('fornecedores.index')
            ->with(
                'sucesso',
                'Fornecedor cadastrado com sucesso! Aguarde a aprovação.'
            );
    }

    public function show(Fornecedor $fornecedor)
    {
        return view('fornecedores.show', compact('fornecedor'));
    }

    public function edit(Fornecedor $fornecedor)
    {
        if (
            auth()->id() !== $fornecedor->user_id &&
            auth()->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        return view(
            'site.testes.fornecedores.edit',
            compact('fornecedor')
        );
    }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        if (
            auth()->id() !== $fornecedor->user_id &&
            auth()->user()->user_type !== 'admin'
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
        ]);

        $fornecedor->update($dados);

        return redirect()
            ->route('fornecedores.show', $fornecedor)
            ->with('sucesso', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Fornecedor $fornecedor)
    {
        if (
            auth()->id() !== $fornecedor->user_id &&
            auth()->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $fornecedor->delete();

        return redirect()
            ->route('fornecedores.index')
            ->with('sucesso', 'Fornecedor excluído com sucesso!');
    }
}
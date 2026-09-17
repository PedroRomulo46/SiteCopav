<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    public function index()
    {
        $fornecedores = Fornecedor::all();

        return view('site.testes.fornecedores.index', compact('fornecedores'));
    }

    public function create()
{
        $usuarios = \App\Models\User::where('user_type', 'fornecedor')->get();

        return view(
            'site.testes.fornecedores.create',
            compact('usuarios')
    );
}

    public function store(Request $request)
    {
        $dados = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:255|unique:fornecedores,documento',
            'telefone' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'status' => 'required|in:pendente,ativo,rejeitado,bloqueado',
        ]);

        Fornecedor::create($dados);

        return redirect()
            ->route('fornecedores.index')
            ->with('sucesso', 'Fornecedor cadastrado com sucesso!');
    }

    public function show(Fornecedor $fornecedor)
    {
        return view('site.testes.fornecedores.show', compact('fornecedor'));
    }

    public function edit(Fornecedor $fornecedor)
    {
        return view('site.testes.fornecedores.edit', compact('fornecedor'));
    }

    public function update(Request $request, Fornecedor $fornecedor)
    {
        $dados = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:255|unique:fornecedores,documento,' . $fornecedor->id,
            'telefone' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'status' => 'required|in:pendente,ativo,rejeitado,bloqueado',
        ]);

        $fornecedor->update($dados);

        return redirect()
            ->route('fornecedores.index')
            ->with('sucesso', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Fornecedor $fornecedor)
    {
        $fornecedor->delete();

        return redirect()
            ->route('fornecedores.index')
            ->with('sucesso', 'Fornecedor excluído com sucesso!');
    }
}

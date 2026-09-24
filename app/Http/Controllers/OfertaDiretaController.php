<?php

namespace App\Http\Controllers;

use App\Models\OfertaDireta;
use App\Models\Demanda;
use App\Models\Fornecedor;
use Illuminate\Http\Request;

class OfertaDiretaController extends Controller
{
    public function index()
    {
        $ofertasDiretas = OfertaDireta::with([
            'demanda',
            'fornecedor'
        ])->get();

        return view('ofertas_diretas.index', compact('ofertasDiretas'));
    }

    public function create()
    {
        $demandas = Demanda::where('status', 'aberta')
            ->with([
                'cliente',
                'categoria'
            ])
            ->get();

        $fornecedores = Fornecedor::where('status', 'ativo')->get();

        return view('ofertas_diretas.create', compact('demandas', 'fornecedores'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'demanda_id' => 'required|exists:demandas,id',
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'observacao' => 'nullable|string',
        ]);

        $dados['status'] = 'pendente';

        OfertaDireta::create($dados);

        return redirect()
            ->route('demandas.show', $dados['demanda_id'])
            ->with('sucesso', 'Oferta enviada com sucesso!');
    }

    public function show(OfertaDireta $ofertaDireta)
    {
        $ofertaDireta->load([
            'demanda.cliente',
            'demanda.categoria',
            'fornecedor'
        ]);

        return view('ofertas_diretas.show', compact('ofertaDireta'));
    }

    public function edit(OfertaDireta $ofertaDireta)
    {
        $demandas = Demanda::where('status', 'aberta')->get();
        $fornecedores = Fornecedor::where('status', 'ativo')->get();

        return view('ofertas_diretas.edit', compact('ofertaDireta', 'demandas', 'fornecedores'));
    }

    public function update(Request $request, OfertaDireta $ofertaDireta)
    {
        $dados = $request->validate([
            'demanda_id' => 'required|exists:demandas,id',
            'fornecedor_id' => 'required|exists:fornecedores,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'observacao' => 'nullable|string',
            'status' => 'required|in:pendente,aceita,recusada',
        ]);

        $ofertaDireta->update($dados);

        return redirect()
            ->route('ofertas-diretas.index')
            ->with('sucesso', 'Oferta atualizada com sucesso!');
    }

    public function destroy(OfertaDireta $ofertaDireta)
    {
        $ofertaDireta->delete();

        return redirect()
            ->route('ofertas-diretas.index')
            ->with('sucesso', 'Oferta excluída com sucesso!');
    }
}
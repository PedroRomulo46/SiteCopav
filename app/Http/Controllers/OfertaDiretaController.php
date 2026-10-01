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
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            return redirect()
                ->route('fornecedores.create')
                ->with(
                    'sucesso',
                    'Você precisa cadastrar um fornecedor antes de enviar ofertas.'
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

        $demandas = Demanda::where('status', 'aberta')
            ->with(['cliente', 'categoria'])
            ->get();

        return view(
            'ofertas_diretas.create',
            compact('demandas')
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
            'demanda_id' => 'required|exists:demandas,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'observacao' => 'nullable|string',
        ]);

        $demanda = Demanda::where('id', $dados['demanda_id'])
            ->where('status', 'aberta')
            ->first();

        if (!$demanda) {
            abort(403);
        }

        $dados['fornecedor_id'] = $fornecedor->id;
        $dados['status'] = 'pendente';

        OfertaDireta::create($dados);

        return redirect()
            ->route('demandas.show', $demanda)
            ->with(
                'sucesso',
                'Oferta enviada com sucesso!'
            );
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
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if (
            $ofertaDireta->fornecedor_id !== $fornecedor->id &&
            auth()->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $demandas = Demanda::where('status', 'aberta')
            ->with(['cliente', 'categoria'])
            ->get();

        return view(
            'ofertas_diretas.edit',
            compact('ofertaDireta', 'demandas')
        );
    }

    public function update(Request $request, OfertaDireta $ofertaDireta)
    {
        $fornecedor = $request->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if (
            $ofertaDireta->fornecedor_id !== $fornecedor->id &&
            $request->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $dados = $request->validate([
            'demanda_id' => 'required|exists:demandas,id',
            'quantidade' => 'required|numeric|min:0',
            'valor' => 'required|numeric|min:0',
            'observacao' => 'nullable|string',
            'status' => 'required|in:pendente,aceita,recusada',
        ]);

        $demanda = Demanda::where('id', $dados['demanda_id'])
            ->where('status', 'aberta')
            ->first();

        if (!$demanda && $request->user()->user_type !== 'admin') {
            abort(403);
        }

        $ofertaDireta->update($dados);

        return redirect()
            ->route('ofertas-diretas.index')
            ->with('sucesso', 'Oferta atualizada com sucesso!');
    }

    public function destroy(OfertaDireta $ofertaDireta)
    {
        $fornecedor = auth()->user()->fornecedor;

        if (!$fornecedor) {
            abort(403);
        }

        if (
            $ofertaDireta->fornecedor_id !== $fornecedor->id &&
            auth()->user()->user_type !== 'admin'
        ) {
            abort(403);
        }

        $ofertaDireta->delete();

        return redirect()
            ->route('ofertas-diretas.index')
            ->with('sucesso', 'Oferta excluída com sucesso!');
    }
}
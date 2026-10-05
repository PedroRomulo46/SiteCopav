<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Oferta;
use App\Models\Demanda;
use App\Models\Negociacao;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Vitrine de Ofertas Globais
        $produtos = Oferta::with(['produto', 'fornecedor'])
            ->latest()
            ->take(9)
            ->get();

        // 2. Lotes do Usuário Logado
        $ofertas = (auth()->check() && auth()->user()->fornecedor)
            ? Oferta::with([
                'produto',
                'negociacoes'
            ])
                ->where('fornecedor_id', auth()->user()->fornecedor->id)
                ->latest()
                ->get()
            : collect();

        // 3. Novas propostas recebidas pelo fornecedor
        $novasPropostas = 0;
        $ofertasComNovasPropostas = collect();

        if (auth()->check() && auth()->user()->fornecedor) {

            $fornecedorId = auth()->user()->fornecedor->id;

            $negociacoesComNovasPropostas = Negociacao::with('oferta')
                ->whereNull('fornecedor_visualizada_em')
                ->whereHas('oferta', function ($query) use ($fornecedorId) {
                    $query->where('fornecedor_id', $fornecedorId);
                })
                ->whereHas('propostas', function ($query) {
                    $query->where('status', 'pendente');
                })
                ->get();

            $novasPropostas = $negociacoesComNovasPropostas->count();

            $ofertasComNovasPropostas =
                $negociacoesComNovasPropostas->keyBy('oferta_id');
        }

        // 4. Demandas da Empresa
        if (
            auth()->check() &&
            (auth()->user()->is_admin ||
             auth()->user()->user_type === 'admin')
        ) {
            $demandas = Demanda::latest()->get();
        } else {
            $demandas = Demanda::where('status', 'aberta')
                ->latest()
                ->get();
        }

        return view('home', compact(
            'produtos',
            'ofertas',
            'demandas',
            'novasPropostas',
            'ofertasComNovasPropostas'
        ));
    }
}
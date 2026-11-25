<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\Demanda;
use App\Models\Proposta;

class HomeController extends Controller
{
    public function index()
    {
        $usuario = auth()->user();

        // 1. Vitrine pública de ofertas
        $produtos = Oferta::with(['produto', 'fornecedor'])
            ->latest()
            ->take(9)
            ->get();

        // 2. Ofertas e novas propostas apenas para Fornecedor comum (não para Admin)
        $ofertas = collect();
        $novasPropostas = 0;
        $ofertasComNovasPropostas = collect();

        if ($usuario && $usuario->isFornecedor()) {
            $fornecedorId = $usuario->fornecedor->id;

            $ofertas = Oferta::with(['produto', 'negociacoes'])
                ->where('fornecedor_id', $fornecedorId)
                ->latest()
                ->get();

            $propostasNaoVisualizadas = Proposta::whereNull('visualizada_em')
                ->where('status', 'pendente')
                ->whereHas('negociacao.oferta', function ($query) use ($fornecedorId) {
                    $query->where('fornecedor_id', $fornecedorId);
                })
                ->with('negociacao')
                ->get();

            $novasPropostas = $propostasNaoVisualizadas->count();

            $ofertasComNovasPropostas = $propostasNaoVisualizadas
                ->groupBy(function ($proposta) {
                    return $proposta->negociacao->oferta_id;
                })
                ->map(function ($propostas) {
                    return $propostas->count();
                });

            $ofertas = $ofertas->sort(function ($a, $b) use ($ofertasComNovasPropostas) {
                $novasA = $ofertasComNovasPropostas->get($a->id, 0);
                $novasB = $ofertasComNovasPropostas->get($b->id, 0);

                if ($novasA !== $novasB) {
                    return $novasB <=> $novasA;
                }

                return $b->id <=> $a->id;
            })->values();
        }

        // 3. Demandas na Home conforme o papel
        if ($usuario && $usuario->isCliente()) {
            $demandas = collect();
        } elseif ($usuario && $usuario->isAdmin()) {
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
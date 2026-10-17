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

        // 2. Ofertas do fornecedor logado
        $ofertas = ($usuario && $usuario->fornecedor)
            ? Oferta::with(['produto', 'negociacoes'])
                ->where('fornecedor_id', $usuario->fornecedor->id)
                ->latest()
                ->get()
            : collect();

        // 3. Propostas pendentes ainda não visualizadas
        $novasPropostas = 0;
        $ofertasComNovasPropostas = collect();

        if ($usuario && $usuario->fornecedor) {
            $fornecedorId = $usuario->fornecedor->id;

            $propostasNaoVisualizadas = Proposta::whereNull('visualizada_em')
                ->where('status', 'pendente')
                ->whereHas('negociacao.oferta', function ($query) use ($fornecedorId) {
                    $query->where('fornecedor_id', $fornecedorId);
                })
                ->with('negociacao')
                ->get();

            // Quantidade total de propostas não visualizadas
            $novasPropostas = $propostasNaoVisualizadas->count();

            // Quantidade de propostas não visualizadas por oferta
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

                    // Mais propostas não visualizadas primeiro
                    if ($novasA !== $novasB) {
                        return $novasB <=> $novasA;
                    }

                    // Em caso de empate, oferta mais recente primeiro
                    return $b->id <=> $a->id;
                })->values();

        }

        // 4. Demandas: clientes não recebem esta lista na Home
        if ($usuario && $usuario->user_type === 'cliente') {
            $demandas = collect();
        } elseif (
            $usuario &&
            ($usuario->is_admin || $usuario->user_type === 'admin')
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
<?php

namespace App\Http\Controllers;

use App\Models\Demanda;
use App\Models\Fornecedor;
use App\Models\Negociacao;
use App\Models\Oferta;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Métricas e dados estratégicos para o painel da Cooperativa
        $totalFornecedores = Fornecedor::count();
        $totalDemandasAbertas = Demanda::where('status', 'aberta')->count();
        $totalNegociacoesEmAndamento = Negociacao::where('status', 'em_negociacao')->count();
        
        $demandasRecentes = Demanda::latest()->take(5)->get();
        $ultimasOfertasFornecedores = Oferta::with(['produto', 'fornecedor'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalFornecedores',
            'totalDemandasAbertas',
            'totalNegociacoesEmAndamento',
            'demandasRecentes',
            'ultimasOfertasFornecedores'
        ));
    }
}
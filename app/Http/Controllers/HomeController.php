<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Oferta;
use App\Models\Demanda;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Vitrine de Ofertas Globais
        $produtos = Oferta::with(['produto', 'fornecedor'])->latest()->take(9)->get();

        // 2. Lotes do Usuário Logado
        $ofertas = (auth()->check() && auth()->user()->fornecedor)
            ? Oferta::with('produto')->where('fornecedor_id', auth()->user()->fornecedor->id)->latest()->get()
            : collect();

        // 3. Demandas da Empresa
        // Se quem está logado for a Cooperativa (Admin), ela vê todas as demandas criadas pela empresa
        // Se for um fornecedor comum, vê as demandas que estão abertas para enviar proposta
        if (auth()->check() && (auth()->user()->is_admin || auth()->user()->user_type === 'admin')) {
            $demandas = Demanda::latest()->get();
        } else {
            $demandas = Demanda::where('status', 'aberta')->latest()->get();
        }

        return view('home', compact('produtos', 'ofertas', 'demandas'));
    }
}
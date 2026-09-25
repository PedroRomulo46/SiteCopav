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
        // Vitrine da direita: traz as ofertas ativas no mercado
        $produtos = Oferta::with(['produto', 'fornecedor'])->latest()->take(9)->get();

        // "Meus Lotes": Ofertas criadas pelo fornecedor logado
        $ofertas = (auth()->check() && auth()->user()->fornecedor)
            ? Oferta::with('produto')->where('fornecedor_id', auth()->user()->fornecedor->id)->latest()->get()
            : Oferta::with('produto')->latest()->take(10)->get();

        // Demandas da Empresa
        $demandas = Demanda::where('status', 'aberta')->latest()->get();

        return view('home', compact('produtos', 'ofertas', 'demandas'));
    }
}
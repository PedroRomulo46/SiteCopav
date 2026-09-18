<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Oferta;
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

    // "Demandas da Empresa": [Tem que criar o Model/Tabela de demandas]
    // O envio limpo está garantido até a criação do Model
    $demandas = class_exists('\App\Models\Demanda') 
        ? \App\Models\Demanda::where('status', 'aberta')->latest()->get() 
        : collect();

    return view('site.home', compact('produtos', 'ofertas', 'demandas'));
}
}
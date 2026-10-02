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
        // Vitrine da direita: traz as ofertas ativas no mercado para todos
        $produtos = Oferta::with(['produto', 'fornecedor'])->latest()->take(9)->get();

        // "Meus Lotes": Somente se o usuário estiver logado E for um fornecedor
        $ofertas = (auth()->check() && auth()->user()->fornecedor)
            ? Oferta::with('produto')->where('fornecedor_id', auth()->user()->fornecedor->id)->latest()->get()
            : collect(); // Retorna coleção vazia para quem não é fornecedor ou é visitante

        // Demandas da Empresa
        $demandas = Demanda::where('status', 'aberta')->latest()->get();

        return view('home', compact('produtos', 'ofertas', 'demandas'));
    }
}
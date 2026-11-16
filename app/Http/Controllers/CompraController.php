<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Negociacao;

class CompraController extends Controller
{
    public function encomenda(Negociacao $negociacao)
    {
        // Garante que o usuário logado é o dono da negociação
        $oferta = $negociacao->oferta;
        $produto = optional($oferta)->produto;

        return view('compras.encomenda', compact('negociacao', 'oferta', 'produto'));
    }
}
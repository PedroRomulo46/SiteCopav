<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Oferta;

class BuscaController extends Controller
{
    public function index(Request $request)
    {
        $termo = $request->input('q');

        // Pesquisa nas ofertas ou produtos que correspondam ao termo digitado
        $ofertas = Oferta::with(['produto', 'fornecedor'])
            ->whereHas('produto', function ($query) use ($termo) {
                $query->where('nome', 'like', "%{$termo}%")
                      ->orWhere('descricao', 'like', "%{$termo}%");
            })
            ->orWhere('status', 'like', "%{$termo}%")
            ->get();

        return view('busca.resultado', compact('ofertas', 'termo'));
    }
}
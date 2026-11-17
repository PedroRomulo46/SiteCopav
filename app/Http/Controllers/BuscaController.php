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

    public function sugestoes(Request $request)
    {
    $termo = trim($request->get('q', ''));

    if (strlen($termo) < 2) {
        return response()->json([]);
    }

    $ofertas = \App\Models\Oferta::with(['produto', 'fornecedor'])
        ->whereHas('produto', function ($query) use ($termo) {
            $query->where('nome', 'like', "%{$termo}%");
        })
        ->orWhereHas('fornecedor', function ($query) use ($termo) {
            $query->where('nome', 'like', "%{$termo}%");
        })
        ->take(6) // Limita a 6 sugestões rápidas no dropdown
        ->get()
        ->map(function ($oferta) {
            $img = $oferta->produto->imagem ?? null;
            $imagemUrl = $img
                ? (str_contains($img, 'assets/') ? asset($img) : asset('storage/' . str_replace('public/', '', $img)))
                : asset('assets/milho.png');

            return [
                'id' => $oferta->id,
                'nome' => $oferta->produto->nome ?? 'Lote',
                'fornecedor' => $oferta->fornecedor->nome ?? 'Copav',
                'valor' => number_format($oferta->valor, 2, ',', '.'),
                'unidade' => $oferta->unidade,
                'imagem' => $imagemUrl,
                'url' => route('ofertas.show', $oferta->id),
            ];
        });

    return response()->json($ofertas);
    }
}
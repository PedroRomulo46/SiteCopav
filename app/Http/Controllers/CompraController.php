<?php

namespace App\Http\Controllers;

use App\Models\Negociacao;

class CompraController extends Controller
{
    public function encomenda(Negociacao $negociacao)
    {
        $usuario = auth()->user();

        $negociacao->loadMissing(['oferta.produto', 'oferta.fornecedor', 'cliente']);

        $isAdmin = $usuario?->isAdmin() ?? false;
        $isClienteDaNegociacao = (int) $negociacao->cliente_id === (int) $usuario?->id;
        $isFornecedorDaOferta = $usuario?->isFornecedor()
            && $negociacao->oferta?->fornecedor
            && (int) $negociacao->oferta->fornecedor->id === (int) $usuario->fornecedor->id;

        if (! $isAdmin && ! $isClienteDaNegociacao && ! $isFornecedorDaOferta) {
            abort(403, 'Você não tem permissão para acessar esta encomenda.');
        }

        $oferta = $negociacao->oferta;
        $produto = optional($oferta)->produto;

        return view('compras.encomenda', compact('negociacao', 'oferta', 'produto'));
    }
}
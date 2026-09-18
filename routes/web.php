<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\NegociacaoController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\HomeController;

# Rota Página Inicial 
Route::get('/', [HomeController::class, 'index'])->name('home');

// Tela de Cadastrar Lote (simulação)
Route::get('/lotes/cadastrar', function () {
    return view('site.cadastrar');
});

// Resources
Route::resource('categorias', CategoriaController::class);
Route::resource('fornecedores', FornecedorController::class);
Route::resource('produtos', ProdutoController::class);
Route::resource('ofertas', OfertaController::class);

Route::resource('negociacoes', NegociacaoController::class)
    ->parameters([
        'negociacoes' => 'negociacao'
    ]);

// Propostas
Route::get(
    '/negociacoes/{negociacao}/propostas/create',
    [PropostaController::class, 'create']
)->name('propostas.create');

Route::post(
    '/propostas',
    [PropostaController::class, 'store']
)->name('propostas.store');
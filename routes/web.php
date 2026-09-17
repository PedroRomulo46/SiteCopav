
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\NegociacaoController;
use App\Http\Controllers\PropostaController;

# Rota página 
Route::get('/', function () {
    return view('site.home');
});

// Tela de Cadastrar Lote (simulação)
Route::get('/lotes/cadastrar', function () {
    return view('site.cadastrar');
});

// Tela de Detalhes do Produto (simulação)
Route::get('/produtos/detalhes', function () {
    return view('site.show');
});

// Categorias
Route::resource('categorias', CategoriaController::class);


// Fornecedores
Route::resource('fornecedores', FornecedorController::class);


// Produtos
Route::resource('produtos', ProdutoController::class);


// Ofertas
Route::resource('ofertas', OfertaController::class);


// Negociações
Route::resource('negociacoes', NegociacaoController::class);


// Propostas
Route::get(
    '/negociacoes/{negociacao}/propostas/create',
    [PropostaController::class, 'create']
)->name('propostas.create');

Route::post(
    '/propostas',
    [PropostaController::class, 'store']
)->name('propostas.store');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\NegociacaoController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DemandaController;
use App\Http\Controllers\OfertaDiretaController;
use App\Http\Controllers\ChatController;

// ROTAS PÚBLICAS (Visitantes e Autenticados)

// Apenas a Home e Categorias são públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('categorias', CategoriaController::class)->only(['index', 'show']);

// ROTAS PROTEGIDAS (Exigem Login)

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->middleware('verified')->name('dashboard');

    // Lotes / Ofertas (Restrito a usuários logados)
    Route::resource('ofertas', OfertaController::class);

    // Demandas (Restrito a usuários logados)
    Route::resource('demandas', DemandaController::class);

    // Produtos
    Route::resource('produtos', ProdutoController::class)->parameters(['produtos' => 'produto']);
    Route::get('/meus-produtos', [ProdutoController::class, 'meusProdutos'])->name('produtos.meus');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    // Categorias (Ações administrativas)
    Route::resource('categorias', CategoriaController::class)->except(['index', 'show']);

    // Recursos de Fornecedor e Negociações
    Route::resource('fornecedores', FornecedorController::class)->parameters(['fornecedores' => 'fornecedor']);
    Route::resource('ofertas-diretas', OfertaDiretaController::class);
    Route::resource('negociacoes', NegociacaoController::class)->parameters(['negociacoes' => 'negociacao']);
    
    // Propostas
    Route::get('/negociacoes/{negociacao}/propostas/create', [PropostaController::class, 'create'])->name('propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])->name('propostas.store');
    Route::patch('/propostas/{proposta}/aceitar', [PropostaController::class, 'aceitar'])->name('propostas.aceitar');
    Route::patch('/propostas/{proposta}/recusar', [PropostaController::class, 'recusar'])->name('propostas.recusar');
    Route::get('/ofertas/{oferta}/propostas', [NegociacaoController::class, 'propostasOferta'])->name('ofertas.propostas');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
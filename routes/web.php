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
use App\Http\Controllers\AdminDashboardController;

// ROTAS PÚBLICAS (Visitantes e Autenticados)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('categorias', CategoriaController::class)->only(['index', 'show']);

// Listagem pública de ofertas
Route::get('/ofertas', [OfertaController::class, 'index'])->name('ofertas.index');

// ROTAS PROTEGIDAS (Exigem Login)
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->middleware('verified')->name('dashboard');

    // Lotes / Ofertas (Restrito a usuários logados)
    Route::get('/ofertas/{oferta}/card', [OfertaController::class, 'card'])
      ->name('ofertas.card');

    Route::resource('ofertas', OfertaController::class);

    // Demandas
    Route::resource('demandas', DemandaController::class);

    // Produtos
    Route::resource('produtos', ProdutoController::class)->parameters(['produtos' => 'produto']);
    Route::get('/meus-produtos', [ProdutoController::class, 'meusProdutos'])->name('produtos.meus');

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    // Categorias
    Route::resource('categorias', CategoriaController::class)->except(['index', 'show']);

    // Recursos de Fornecedor e Negociações
    Route::resource('fornecedores', FornecedorController::class)->parameters(['fornecedores' => 'fornecedor']);
    Route::resource('ofertas-diretas', OfertaDiretaController::class);
    Route::resource('negociacoes', NegociacaoController::class)->parameters(['negociacoes' => 'negociacao']);
    Route::post(
        '/negociacoes/{negociacao}/visualizada',
        [NegociacaoController::class, 'marcarComoVisualizada']
    )->name('negociacoes.visualizada');
    
    // Propostas
    Route::get('/negociacoes/{negociacao}/propostas/create', [PropostaController::class, 'create'])->name('propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])->name('propostas.store');
    Route::patch('/propostas/{proposta}/aceitar', [PropostaController::class, 'aceitar'])->name('propostas.aceitar');
    Route::patch('/propostas/{proposta}/recusar', [PropostaController::class, 'recusar'])->name('propostas.recusar');
    Route::get('/ofertas/{oferta}/propostas', [NegociacaoController::class, 'propostasOferta'])->name('negociacoes.propostas');
    Route::get(
        '/propostas/{proposta}/visualizar',
        [PropostaController::class, 'visualizar']
    )->name('propostas.visualizar');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ROTAS EXCLUSIVAS DA EMPRESA (ADMIN)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

// Rota com parâmetro dinâmico em último para evitar conflitos
Route::get('/ofertas/{oferta}', [OfertaController::class, 'show'])->name('ofertas.show');

require __DIR__.'/auth.php';
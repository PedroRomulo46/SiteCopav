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

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Página inicial
Route::get('/', [HomeController::class, 'index'])->name('home');

// Visualização de Categorias e Produtos (Públicos)
Route::resource('categorias', CategoriaController::class)->only(['index', 'show']);
Route::resource('produtos', ProdutoController::class)->only(['index', 'show']);

// Visualização de Ofertas (Públicas)
Route::get('/ofertas', [OfertaController::class, 'index'])->name('ofertas.index');

/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (Exigem Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Dashboard (Redireciona para home)
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->middleware('verified')->name('dashboard');

    // Página de Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    // Criação/Ações de Ofertas (Obrigatório vir antes da rota pública de show /{oferta})
    Route::get('/ofertas/create', [OfertaController::class, 'create'])->name('ofertas.create');
    Route::post('/ofertas', [OfertaController::class, 'store'])->name('ofertas.store');
    Route::get('/ofertas/{oferta}/edit', [OfertaController::class, 'edit'])->name('ofertas.edit');
    Route::put('/ofertas/{oferta}', [OfertaController::class, 'update'])->name('ofertas.update');
    Route::delete('/ofertas/{oferta}', [OfertaController::class, 'destroy'])->name('ofertas.destroy');

    // Outros Recursos
    Route::resource('fornecedores', FornecedorController::class);
    Route::resource('demandas', DemandaController::class);
    Route::resource('ofertas-diretas', OfertaDiretaController::class);

    // Negociações
    Route::resource('negociacoes', NegociacaoController::class)->parameters([
        'negociacoes' => 'negociacao'
    ]);

    // Propostas
    Route::get('/negociacoes/{negociacao}/propostas/create', [PropostaController::class, 'create'])->name('propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])->name('propostas.store');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// Detalhes da Oferta (Público - MANTIDO NO FINAL para não capturar '/ofertas/create' como ID)
Route::get('/ofertas/{oferta}', [OfertaController::class, 'show'])->name('ofertas.show');

/*
|--------------------------------------------------------------------------
| Autenticação do Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
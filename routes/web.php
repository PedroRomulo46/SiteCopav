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

// Visitantes só podem VER listas e detalhes (index, show)
Route::resource('categorias', CategoriaController::class)->only(['index', 'show']);
Route::resource('produtos', ProdutoController::class)->only(['index', 'show']);
Route::resource('ofertas', OfertaController::class)->only(['index', 'show']);

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

    // Ofertas (Apenas Criar, Editar, Atualizar e Deletar)
    Route::resource('ofertas', OfertaController::class)->except(['index', 'show']);

    // Outros Recursos
    Route::resource('fornecedores', FornecedorController::class);
    Route::resource('demandas', DemandaController::class);
    Route::resource('ofertas-diretas', OfertaDiretaController::class);

    // Negociações
    Route::resource('negociacoes', NegociacaoController::class)->parameters([
        'negociacoes' => 'negociacao'
    ]);

    // Propostas
    Route::get('/negociacoes/{negociacao}/propostas/create', [PropostaController::class, 'create'])
        ->name('propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])
        ->name('propostas.store');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Autenticação do Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
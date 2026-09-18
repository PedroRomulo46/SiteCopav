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
use App\Http\Controllers\ChatController;


/*
|--------------------------------------------------------------------------
| Página inicial
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');


/*
|--------------------------------------------------------------------------
| Página de Chat
|--------------------------------------------------------------------------
*/

Route::get('/chat', [ChatController::class, 'index'])->name('chat');

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Recursos
|--------------------------------------------------------------------------
*/

Route::resource('categorias', CategoriaController::class);

Route::resource('fornecedores', FornecedorController::class);

Route::resource('produtos', ProdutoController::class);

Route::resource('ofertas', OfertaController::class);


/*
|--------------------------------------------------------------------------
| Negociações
|--------------------------------------------------------------------------
*/

Route::resource('negociacoes', NegociacaoController::class)
    ->parameters([
        'negociacoes' => 'negociacao'
    ]);


/*
|--------------------------------------------------------------------------
| Propostas
|--------------------------------------------------------------------------
*/

Route::get(
    '/negociacoes/{negociacao}/propostas/create',
    [PropostaController::class, 'create']
)->name('propostas.create');

Route::post(
    '/propostas',
    [PropostaController::class, 'store']
)->name('propostas.store');


/*
|--------------------------------------------------------------------------
| Perfil
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Autenticação do Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
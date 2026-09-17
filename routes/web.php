<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\ProdutoController;

Route::get('/', function () {
    return view('site.home');
});

Route::resource('fornecedores', FornecedorController::class);

Route::resource('produtos', ProdutoController::class);
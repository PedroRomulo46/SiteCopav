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
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\BuscaController;
use App\Http\Controllers\WebhookController;

// ROTAS PÚBLICAS (Visitantes e Autenticados)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::resource('categorias', CategoriaController::class)->only(['index', 'show']);

// Listagem pública de ofertas
Route::get('/ofertas', [OfertaController::class, 'index'])->name('ofertas.index');

// Exibição do card de oferta
Route::resource('ofertas', OfertaController::class);

// Pesquisa
Route::get('/buscar', [BuscaController::class, 'index'])->name('buscar');
Route::get('/buscar/sugestoes', [BuscaController::class, 'sugestoes'])->name('buscar.sugestoes');

// Rota do Webhook do Stripe (Pública, pois vem de fora da aplicação)
Route::post('/webhook/stripe', [WebhookController::class, 'stripe']);

// ROTAS PROTEGIDAS (Exigem Login)
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {return redirect()->route('home');})->middleware('verified')->name('dashboard');

    // Lotes / Ofertas (Restrito a usuários logados)
    Route::get('/ofertas/{oferta}/card', [OfertaController::class, 'card'])->name('ofertas.card');
    Route::get('/propostas/{proposta}/card', [PropostaController::class, 'card'])->name('propostas.card');
        
    // Demandas
    Route::resource('demandas', DemandaController::class);
        
    // Produtos
    Route::resource('produtos', ProdutoController::class)->parameters(['produtos' => 'produto']);
    Route::get('/meus-produtos', [ProdutoController::class, 'meusProdutos'])->name('produtos.meus');

    
    // Chat — nova estrutura por cliente e fornecedor

    // Página principal
    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat');

    // Lista de conversas para a janela flutuante
    Route::get('/chat/conversas', [ChatController::class, 'conversas'])
        ->name('chat.conversas');

    // Abrir uma conversa específica
    Route::get('/chat/{chat}', [ChatController::class, 'show'])
        ->whereNumber('chat')
        ->name('chat.show');

    // Listar assuntos de uma conversa
    Route::get('/chat/{chat}/assuntos', [ChatController::class, 'assuntos'])
        ->whereNumber('chat')
        ->name('chat.assuntos');

    // Histórico de mensagens gerais ou de um assunto
    Route::get('/chat/{chat}/mensagens', [ChatController::class, 'mensagens'])
        ->whereNumber('chat')
        ->name('chat.mensagens');

    // Enviar mensagem geral ou vinculada a um assunto
    Route::post('/chat/{chat}/mensagens', [ChatController::class, 'store'])
        ->whereNumber('chat')
        ->name('chat.mensagens.store');

    // Abrir conversa a partir de uma negociação
    Route::get('/negociacoes/{negociacao}/chat', [ChatController::class, 'abrirNegociacao'])
        ->name('negociacoes.chat');

    // Categorias
    Route::resource('categorias', CategoriaController::class)->except(['index', 'show']);
    
    // Recursos de Fornecedor e Negociações
    Route::resource('fornecedores', FornecedorController::class)->parameters(['fornecedores' => 'fornecedor']);
    Route::resource('ofertas-diretas', OfertaDiretaController::class);
    
    // Compras do usuário autenticado
    Route::get('/negociacoes/compras', [NegociacaoController::class, 'compras'])->name('negociacoes.compras');
    
    // Vendas: fornecedores e administradores
    Route::get('/negociacoes/vendas', [NegociacaoController::class, 'vendas'])->name('negociacoes.vendas');
    
    // Recurso geral de negociações — declarar apenas uma vez
    Route::resource('negociacoes', NegociacaoController::class)
    ->parameters(['negociacoes' => 'negociacao']);
    
    // Marcar negociação como visualizada
    Route::post('/negociacoes/{negociacao}/visualizada', [NegociacaoController::class, 'marcarComoVisualizada'])
    ->name('negociacoes.visualizada');
    
    // Propostas
    Route::get('/negociacoes/{negociacao}/propostas/create', [PropostaController::class, 'create'])->name('propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])->name('propostas.store');
    Route::patch('/propostas/{proposta}/aceitar', [PropostaController::class, 'aceitar'])->name('propostas.aceitar');
    Route::patch('/propostas/{proposta}/recusar', [PropostaController::class, 'recusar'])->name('propostas.recusar');
    Route::patch('/propostas/{proposta}/cancelar', [PropostaController::class, 'cancelar'])->name('propostas.cancelar');
    Route::get('/ofertas/{oferta}/propostas', [NegociacaoController::class, 'propostasOferta'])->name('negociacoes.propostas');
    Route::get('/propostas/{proposta}/visualizar', [PropostaController::class, 'visualizar'])->name('propostas.visualizar');

    // Perfil
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logística
    Route::get('/compras/{negociacao}/encomenda', [App\Http\Controllers\CompraController::class, 'encomenda'])->name('compras.encomenda');

    // Pagamentos
    Route::get('/negociacoes/{negociacao}/pagamento', [PagamentoController::class, 'show'])->name('pagamentos.show');
    Route::post('/negociacoes/{negociacao}/pagamento/aprovar', [PagamentoController::class, 'confirmar'])->name('pagamentos.confirmar');
    Route::get('/negociacoes/{negociacao}/pagamento/sucesso', [PagamentoController::class, 'sucesso'])->name('pagamentos.sucesso');
});

// ROTAS EXCLUSIVAS DA EMPRESA (ADMIN)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/auth.php';
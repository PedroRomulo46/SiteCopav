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
use App\Http\Controllers\CompraController;

// =========================================================================
// ROTAS PÚBLICAS (Visitantes e Autenticados)
// =========================================================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ofertas públicas (index e show; create/store/edit/update/destroy protegidos abaixo)
Route::resource('ofertas', OfertaController::class)->only(['index', 'show']);

// Categorias públicas (index e show apenas para ID numérico)
Route::resource('categorias', CategoriaController::class)
    ->only(['index', 'show'])
    ->where(['categoria' => '[0-9]+']);

// Pesquisa
Route::get('/buscar', [BuscaController::class, 'index'])->name('buscar');
Route::get('/buscar/sugestoes', [BuscaController::class, 'sugestoes'])->name('buscar.sugestoes');

// Webhook do Stripe (Público)
Route::post('/webhook/stripe', [WebhookController::class, 'stripe']);

// =========================================================================
// ROTAS AUTENTICADAS (Todos os papéis logados: Cliente, Fornecedor, Admin)
// =========================================================================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->middleware('verified')->name('dashboard');

    // Cards dinâmicos (usados via fetch/Echo)
    Route::get('/ofertas/{oferta}/card', [OfertaController::class, 'card'])->name('ofertas.card');
    Route::get('/propostas/{proposta}/card', [PropostaController::class, 'card'])->name('propostas.card');

    // Visualização de Demandas (qualquer usuário logado pode consultar)
    Route::get('/demandas', [DemandaController::class, 'index'])->name('demandas.index');

    // Sugestão de categoria (qualquer usuário autenticado pode sugerir)
    Route::get('/categorias/sugerir', [CategoriaController::class, 'sugerir'])->name('categorias.sugerir');
    Route::post('/categorias/sugerir', [CategoriaController::class, 'armazenarSugestao'])->name('categorias.sugerir.store');

    // Cadastro / gestão do perfil de Fornecedor do usuário logado
    Route::resource('fornecedores', FornecedorController::class)->parameters(['fornecedores' => 'fornecedor']);

    // Chat
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/conversas', [ChatController::class, 'conversas'])->name('chat.conversas');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->whereNumber('chat')->name('chat.show');
    Route::get('/chat/{chat}/assuntos', [ChatController::class, 'assuntos'])->whereNumber('chat')->name('chat.assuntos');
    Route::get('/chat/{chat}/mensagens', [ChatController::class, 'mensagens'])->whereNumber('chat')->name('chat.mensagens');
    Route::post('/chat/{chat}/mensagens', [ChatController::class, 'store'])->whereNumber('chat')->name('chat.mensagens.store');
    Route::get('/negociacoes/{negociacao}/chat', [ChatController::class, 'abrirNegociacao'])->name('negociacoes.chat');

    // Negociações, Compras, Vendas e Propostas
    // Declarar rotas estáticas (/compras e /vendas) ANTES do resource de negociacoes
    Route::get('/negociacoes/compras', [NegociacaoController::class, 'compras'])->name('negociacoes.compras');
    Route::get('/negociacoes/vendas', [NegociacaoController::class, 'vendas'])->name('negociacoes.vendas');

    Route::resource('negociacoes', NegociacaoController::class)
        ->parameters(['negociacoes' => 'negociacao']);

    Route::post('/negociacoes/{negociacao}/visualizada', [NegociacaoController::class, 'marcarComoVisualizada'])
        ->name('negociacoes.visualizada');

    Route::get('/negociacoes/{negociacao}/propostas/create', [PropostaController::class, 'create'])->name('propostas.create');
    Route::post('/propostas', [PropostaController::class, 'store'])->name('propostas.store');
    Route::patch('/propostas/{proposta}/aceitar', [PropostaController::class, 'aceitar'])->name('propostas.aceitar');
    Route::patch('/propostas/{proposta}/recusar', [PropostaController::class, 'recusar'])->name('propostas.recusar');
    Route::patch('/propostas/{proposta}/cancelar', [PropostaController::class, 'cancelar'])->name('propostas.cancelar');
    Route::get('/ofertas/{oferta}/propostas', [NegociacaoController::class, 'propostasOferta'])->name('negociacoes.propostas');
    Route::get('/propostas/{proposta}/visualizar', [PropostaController::class, 'visualizar'])->name('propostas.visualizar');

    // Perfil do usuário
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logística e Pagamentos
    Route::get('/compras/{negociacao}/encomenda', [CompraController::class, 'encomenda'])->name('compras.encomenda');
    Route::get('/negociacoes/{negociacao}/pagamento', [PagamentoController::class, 'show'])->name('pagamentos.show');
    Route::post('/negociacoes/{negociacao}/pagamento/aprovar', [PagamentoController::class, 'confirmar'])->name('pagamentos.confirmar');
    Route::get('/negociacoes/{negociacao}/pagamento/sucesso', [PagamentoController::class, 'sucesso'])->name('pagamentos.sucesso');

    // =====================================================================
    // ROTAS DE FORNECEDOR (Produtos, Ofertas e Ofertas Diretas)
    // =====================================================================
    Route::middleware('role:fornecedor')->group(function () {
        Route::get('/meus-produtos', [ProdutoController::class, 'meusProdutos'])->name('produtos.meus');
        Route::resource('produtos', ProdutoController::class)->parameters(['produtos' => 'produto']);

        Route::resource('ofertas', OfertaController::class)->except(['index', 'show']);
        Route::resource('ofertas-diretas', OfertaDiretaController::class);
    });

    // =====================================================================
    // ROTAS DE FORNECEDOR E ADMIN (Criação/Gestão de Demandas)
    // =====================================================================
    Route::middleware('role:fornecedor,admin')->group(function () {
        Route::resource('demandas', DemandaController::class)->except(['index']);
    });

    // =====================================================================
    // ROTAS EXCLUSIVAS DO ADMINISTRADOR
    // =====================================================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('categorias', CategoriaController::class)->except(['index', 'show']);
        Route::post('/categorias/sugestoes/{sugestao}/aprovar', [CategoriaController::class, 'aprovarSugestao'])
            ->name('categorias.sugerir.aprovar');
        Route::delete('/categorias/sugestoes/{sugestao}', [CategoriaController::class, 'recusarSugestao'])
            ->name('categorias.sugerir.recusar');
    });
});

// Rota show de demandas após /demandas/create para não conflitar com 'create'
Route::middleware('auth')->get('/demandas/{demanda}', [DemandaController::class, 'show'])->name('demandas.show');

require __DIR__.'/auth.php';
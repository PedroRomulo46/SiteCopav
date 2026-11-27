@extends('layouts.layout')

@section('title', $produto->nome ?? 'Detalhes da Oferta')

@section('conteudo')

@php
    $usuarioLogado = auth()->user();
    $fornecedorDoUsuario = $usuarioLogado?->fornecedor;

    $isDonoDaOferta = $usuarioLogado !== null && (
        ($oferta->user_id !== null && (int) $usuarioLogado->id === (int) $oferta->user_id)
        || ($fornecedorDoUsuario && (int) ($oferta->fornecedor_id ?? 0) === (int) $fornecedorDoUsuario->id)
    );

    $podeComentarNaOferta = false;
    if ($usuarioLogado !== null) {
        if ($usuarioLogado->isAdmin()) {
            $podeComentarNaOferta = true;
        } else {
            $clienteIdUsuario = $usuarioLogado->cliente?->id ? (int) $usuarioLogado->cliente->id : null;
            $temColunaUserId = \Illuminate\Support\Facades\Schema::hasColumn('negociacoes', 'user_id');
            $temColunaUsuarioId = \Illuminate\Support\Facades\Schema::hasColumn('negociacoes', 'usuario_id');
            $temColunaClienteId = \Illuminate\Support\Facades\Schema::hasColumn('negociacoes', 'cliente_id');

            $podeComentarNaOferta = \App\Models\Negociacao::where('oferta_id', $oferta->id)
                ->whereIn('status', ['pago', 'aprovado'])
                ->where(function ($query) use (
                    $usuarioLogado,
                    $clienteIdUsuario,
                    $temColunaUserId,
                    $temColunaUsuarioId,
                    $temColunaClienteId
                ) {
                    $temCondicao = false;

                    if ($temColunaUserId) {
                        $query->where('user_id', $usuarioLogado->id);
                        $temCondicao = true;
                    }

                    if ($temColunaUsuarioId) {
                        $metodo = $temCondicao ? 'orWhere' : 'where';
                        $query->{$metodo}('usuario_id', $usuarioLogado->id);
                        $temCondicao = true;
                    }

                    if ($temColunaClienteId && $clienteIdUsuario !== null) {
                        $metodo = $temCondicao ? 'orWhere' : 'where';
                        $query->{$metodo}('cliente_id', $clienteIdUsuario);
                        $temCondicao = true;
                    }

                    $metodoProposta = $temCondicao ? 'orWhereHas' : 'whereHas';
                    $query->{$metodoProposta}('propostas', function ($q) use ($usuarioLogado) {
                        $q->where('usuario_id', $usuarioLogado->id)
                            ->where('status', 'aceita');
                    });
                })
                ->exists();
        }
    }

    $imagemProduto = ($produto && $produto->imagem)
        ? (str_contains($produto->imagem, 'assets/')
            ? asset($produto->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $produto->imagem), '/')))
        : asset('assets/milho.png');

    $fornecedorModel = $oferta->fornecedor;
    $rawFotoFornecedor = $fornecedorModel?->imagem
        ?? $fornecedorModel?->foto
        ?? $fornecedorModel?->avatar
        ?? $fornecedorModel?->usuario?->imagem
        ?? $fornecedorModel?->usuario?->avatar
        ?? null;

    $fotoFornecedorUrl = null;
    if (! empty($rawFotoFornecedor)) {
        if (str_starts_with($rawFotoFornecedor, 'http://') || str_starts_with($rawFotoFornecedor, 'https://')) {
            $fotoFornecedorUrl = $rawFotoFornecedor;
        } elseif (str_contains($rawFotoFornecedor, 'assets/')) {
            $fotoFornecedorUrl = asset($rawFotoFornecedor);
        } else {
            $fotoFornecedorUrl = asset('storage/' . ltrim(str_replace('public/', '', $rawFotoFornecedor), '/'));
        }
    }

    $listaComentarios = collect($comentarios ?? ($oferta->comentarios ?? []));
@endphp

{{-- Breadcrumbs com setas garantidas via ícone --}}
<nav class="flex items-center text-sm mb-6 text-gray-600 dark:text-gray-400" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-2">

        {{-- Item 1: Início --}}
        <li class="inline-flex items-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-[#1B4D3E] dark:hover:text-emerald-400 transition-colors">
                Início
            </a>
        </li>

        {{-- Item 2: Página Atual --}}
        <li class="inline-flex items-center space-x-2">
            <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-sm leading-none">chevron_right</span>
            <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
                @yield('title')
            </span>
        </li>
    </ol>
</nav>

<div class="max-w-7xl mx-auto space-y-6 text-left">
    <div
        id="oferta-show"
        data-oferta-id="{{ $oferta->id }}"
        class="w-full px-4 sm:px-6 lg:px-8 py-6 bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] rounded-xl shadow-sm text-left"
    >

        {{-- Exibição de Alertas de Sucesso / Erro --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/50 border-l-4 border-emerald-600 dark:border-emerald-400 text-emerald-800 dark:text-emerald-200 rounded-r-md shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 leading-none shrink-0">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-950/50 border-l-4 border-red-500 dark:border-red-400 text-red-700 dark:text-red-300 rounded-r-md shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500 dark:text-red-400 leading-none shrink-0">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Coluna 1: Imagem do Produto -->
            <div class="lg:col-span-5 lg:sticky lg:top-6 w-full">
                <div class="bg-white dark:bg-[#161f30] p-2 rounded-xl border border-gray-200/80 dark:border-[#273449] overflow-hidden w-full h-380px">
                    <img
                        class="w-full h-full rounded-lg object-cover"
                        src="{{ $imagemProduto }}"
                        alt="{{ $produto->nome ?? 'Imagem do produto' }}"
                        onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                    />
                </div>
            </div>

            <!-- Coluna 2: Informações Técnicas e Descrição -->
            <div class="lg:col-span-4 flex flex-col gap-3 bg-white dark:bg-[#111827] px-2 py-1 sm:px-3 rounded-md text-left">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 leading-tight">
                        {{ $produto->nome ?? 'Produto sem nome' }}
                    </h1>
                </div>

                <!-- Avaliações -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1">
                        <span class="text-sm font-bold text-amber-400">4.6</span>
                        <div class="flex text-amber-400 text-base leading-none">
                            ★★★★<span class="text-gray-300 dark:text-slate-600">★</span>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">(1.889 avaliações)</span>
                </div>

                <!-- Status da Oferta -->
                <div class="pt-0.5">
                    <span
                        id="oferta-status"
                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold
                        {{ $oferta->status === 'publicada'
                            ? 'text-green-700 dark:text-emerald-300 bg-green-50 dark:bg-emerald-950/60 border border-green-100 dark:border-emerald-800/60'
                            : ($oferta->status === 'encerrada'
                                ? 'text-yellow-700 dark:text-amber-300 bg-yellow-50 dark:bg-amber-950/60 border border-yellow-100 dark:border-amber-800/60'
                                : 'text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-950/60 border border-red-100 dark:border-red-800/60') }}"
                    >
                        {{ ucfirst($oferta->status ?? 'Indefinido') }}
                    </span>
                </div>

                <!-- Preço e Medida da Oferta -->
                <div class="space-y-1 pt-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                        Preço unitário
                    </span>

                    <div class="flex items-baseline gap-1">
                        <span class="text-lg font-bold text-gray-700 dark:text-gray-300">R$</span>
                        <span
                            id="oferta-valor"
                            class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100"
                        >
                            {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                        </span>
                        <span
                            id="oferta-unidade"
                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                        >
                            / {{ $oferta->unidade ?? 'Unidade' }}
                        </span>
                    </div>
                </div>

                <!-- Descrição detalhada -->
                <div class="pt-3 border-t border-gray-200 dark:border-[#273449]">
                    <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-1.5">Sobre este item</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $produto->descricao ?? 'Nenhuma descrição detalhada informada para este produto.' }}
                    </p>
                </div>
            </div>

            <!-- Coluna 3: Box de Compra / Ações Comerciais -->
            <div class="lg:col-span-3">
                <div class="border border-gray-200 dark:border-[#273449] rounded-xl p-5 flex flex-col gap-4 shadow-sm bg-white dark:bg-[#161f30] text-left">

                    <div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">
                            Valor da oferta
                        </span>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            R$
                            <span id="oferta-valor-box">
                                {{ number_format($oferta->valor ?? 0, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="inline-flex items-center gap-1.5 text-xs text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1.5 rounded-md font-medium border border-emerald-100 dark:border-emerald-800/60">
                        <span class="material-symbols-outlined text-sm leading-none shrink-0">inventory_2</span>

                        <span>
                            Em estoque:

                            <strong>
                                <span id="oferta-quantidade">
                                    @if(isset($oferta->quantidade))
                                        @if(strtolower((string) $oferta->unidade) === 'saca')
                                            {{ number_format($oferta->quantidade, 0, ',', '.') }}
                                        @else
                                            {{ fmod($oferta->quantidade, 1) == 0
                                                ? number_format($oferta->quantidade, 0, ',', '.')
                                                : number_format($oferta->quantidade, 2, ',', '.') }}
                                        @endif
                                    @else
                                        0
                                    @endif
                                </span>

                                <span id="oferta-quantidade-unidade">
                                    {{ $oferta->unidade ?? '' }}
                                </span>
                            </strong>
                        </span>
                    </div>

                    <div class="text-xs text-gray-600 dark:text-gray-300 space-y-1.5 bg-gray-50 dark:bg-[#111827] p-3 rounded-lg border border-gray-200/80 dark:border-[#273449]">
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500 dark:text-gray-400">Enviado por:</span>
                            <span class="text-gray-800 dark:text-gray-100 font-semibold">Copav</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-gray-500 dark:text-gray-400">Vendido por:</span>
                            <span class="text-gray-800 dark:text-gray-100 font-semibold text-right">{{ $fornecedorModel->nome ?? 'Fornecedor' }}</span>
                        </div>
                    </div>

                    <hr class="border-gray-200 dark:border-[#273449]">

                    <!-- Botões de Ação -->
                    <div class="flex flex-col gap-2.5 w-full">
                        @auth
                            @unless($isDonoDaOferta)
                                <form action="{{ route('negociacoes.store') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="oferta_id" value="{{ $oferta->id }}">
                                    <button
                                        type="submit"
                                        class="w-full btn-copav p-3 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:opacity-95 inline-flex items-center justify-center gap-2 cursor-pointer border-0">
                                        <span class="material-symbols-outlined text-base leading-none">currency_exchange</span>
                                        <span>Negociar Preço</span>
                                    </button>
                                </form>
                            @else
                                <div class="text-xs text-center text-gray-500 dark:text-gray-400 py-2 px-3 bg-gray-100 dark:bg-[#111827] border border-transparent dark:border-[#273449] rounded-lg">
                                    Esta oferta pertence a você.
                                </div>
                            @endunless
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="w-full btn-copav p-3 text-white text-sm font-semibold rounded-lg transition-all shadow-sm text-center inline-flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-base leading-none">login</span>
                                <span>Entrar para negociar</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEÇÃO INFERIOR 1: Informações em linha do Fornecedor --}}
    @if($fornecedorModel)
        <section class="w-full px-4 sm:px-6 lg:px-8 py-5 bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] rounded-xl shadow-sm text-left">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">

                {{-- Foto de perfil + Nome + Status --}}
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-emerald-50 dark:bg-emerald-950/60 border border-gray-200 dark:border-[#273449] flex items-center justify-center shrink-0">
                        @if($fotoFornecedorUrl)
                            <img
                                src="{{ $fotoFornecedorUrl }}"
                                alt="Foto de {{ $fornecedorModel->nome }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <span class="material-symbols-outlined text-3xl text-[#236350] dark:text-emerald-400 leading-none">storefront</span>
                        @endif
                    </div>

                    <div class="min-w-0 text-left">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 truncate">
                                {{ $fornecedorModel->nome ?? 'Fornecedor Copav' }}
                            </h2>

                            @if($fornecedorModel->status)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $fornecedorModel->status === 'ativo' ? 'bg-green-100 dark:bg-emerald-950/60 text-green-800 dark:text-emerald-300 border border-transparent dark:border-emerald-800/60' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 border border-transparent dark:border-slate-700' }}">
                                    <span class="material-symbols-outlined text-xs leading-none">{{ $fornecedorModel->status === 'ativo' ? 'check_circle' : 'info' }}</span>
                                    <span>{{ ucfirst($fornecedorModel->status) }}</span>
                                </span>
                            @endif
                        </div>

                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">
                            {{ $fornecedorModel->descricao ?: 'Fornecedor parceiro cadastrado no marketplace Copav.' }}
                        </p>
                    </div>
                </div>

                {{-- Informações em linha --}}
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-xs sm:text-sm text-gray-600 dark:text-gray-300 lg:border-l lg:border-gray-200 dark:lg:border-[#273449] lg:pl-6">
                    @if($fornecedorModel->telefone)
                        <div class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-[#236350] dark:text-emerald-400 leading-none shrink-0">call</span>
                            <span class="text-gray-400 dark:text-gray-500">Telefone:</span>
                            <strong class="text-gray-800 dark:text-gray-100 font-semibold">{{ $fornecedorModel->telefone }}</strong>
                        </div>
                    @endif

                    @if($fornecedorModel->cidade || $fornecedorModel->estado)
                        <div class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-[#236350] dark:text-emerald-400 leading-none shrink-0">location_on</span>
                            <span class="text-gray-400 dark:text-gray-500">Local:</span>
                            <strong class="text-gray-800 dark:text-gray-100 font-semibold">
                                {{ $fornecedorModel->cidade ?? '' }}{{ ($fornecedorModel->cidade && $fornecedorModel->estado) ? ' - ' : '' }}{{ $fornecedorModel->estado ?? '' }}
                            </strong>
                        </div>
                    @endif

                    @if($fornecedorModel->endereco)
                        <div class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-[#236350] dark:text-emerald-400 leading-none shrink-0">home_pin</span>
                            <span class="text-gray-400 dark:text-gray-500">Endereço:</span>
                            <strong class="text-gray-800 dark:text-gray-100 font-semibold">{{ $fornecedorModel->endereco }}</strong>
                        </div>
                    @endif
                </div>

                {{-- Botão para acessar a página completa do fornecedor --}}
                <div class="shrink-0">
                    <a
                        href="{{ route('fornecedores.show', $fornecedorModel) }}"
                        class="btn-copav inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg text-xs sm:text-sm font-semibold shadow-sm transition-colors whitespace-nowrap"
                    >
                        <span class="material-symbols-outlined text-base leading-none">storefront</span>
                        <span>Ver perfil do fornecedor</span>
                    </a>
                </div>

            </div>
        </section>
    @endif

    {{-- SEÇÃO INFERIOR 2: Comentários da Oferta --}}
    <section id="comentarios" class="w-full px-4 sm:px-6 lg:px-8 py-6 bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] rounded-xl shadow-sm text-left">
        <div class="flex items-center justify-between gap-4 pb-4 border-b border-gray-200 dark:border-[#273449]">
            <div class="flex items-center gap-2.5 text-left">
                <span class="material-symbols-outlined text-2xl text-[#236350] dark:text-emerald-400 leading-none shrink-0">forum</span>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">
                        Comentários
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        Avaliações e comentários de compradores que concluíram o pagamento desta oferta
                    </p>
                </div>
            </div>

            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#161f30] text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-[#273449] shrink-0">
                {{ $listaComentarios->count() }} {{ $listaComentarios->count() === 1 ? 'comentário' : 'comentários' }}
            </span>
        </div>

        {{-- Formulário de Novo Comentário (somente após pagamento aprovado) --}}
        <div class="mt-5 w-full text-left">
            @auth
                @if($podeComentarNaOferta && \Illuminate\Support\Facades\Route::has('ofertas.comentarios.store'))
                    <form action="{{ route('ofertas.comentarios.store', $oferta) }}" method="POST" class="space-y-3 w-full">
                        @csrf
                        <div class="w-full">
                            <label for="comentario-conteudo" class="sr-only">Escrever comentário</label>
                            <textarea
                                id="comentario-conteudo"
                                name="conteudo"
                                rows="3"
                                required
                                placeholder="Como foi sua experiência com este lote após a compra?"
                                class="w-full rounded-lg border border-gray-300 dark:border-[#273449] bg-gray-50 dark:bg-[#161f30] px-4 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-[#236350] dark:focus:border-emerald-400 focus:outline-none transition-colors text-left"
                            >{{ old('conteudo') }}</textarea>
                            @error('conteudo')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400 text-left">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end w-full">
                            <button
                                type="submit"
                                class="btn-copav inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold shadow-sm transition-colors cursor-pointer border-0 shrink-0"
                            >
                                <span class="material-symbols-outlined text-base leading-none">send</span>
                                <span>Publicar comentário</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="rounded-lg bg-sky-50/80 dark:bg-sky-950/30 border border-sky-200/80 dark:border-sky-800/50 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-left">
                        <div class="flex items-center gap-2.5 text-xs sm:text-sm text-sky-900 dark:text-sky-200">
                            <span class="material-symbols-outlined text-lg text-sky-600 dark:text-sky-400 leading-none shrink-0">verified</span>
                            <span>
                                Apenas compradores que realizaram o pagamento desta oferta podem comentar.
                            </span>
                        </div>
                        <a
                            href="{{ route('negociacoes.compras') }}"
                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-700 text-white transition-colors whitespace-nowrap shrink-0"
                        >
                            <span class="material-symbols-outlined text-sm leading-none">shopping_bag</span>
                            <span>Ver minhas compras</span>
                        </a>
                    </div>
                @endif
            @else
                <div class="rounded-lg bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-left">
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-300">
                        Entre na sua conta para acompanhar suas compras e comentar após o pagamento.
                    </p>
                    <a
                        href="{{ route('login') }}"
                        class="btn-copav inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition-colors whitespace-nowrap shrink-0"
                    >
                        <span class="material-symbols-outlined text-base leading-none">login</span>
                        <span>Entrar</span>
                    </a>
                </div>
            @endauth
        </div>

        {{-- Lista de Comentários --}}
        <div class="mt-4 space-y-3 w-full text-left">
            @forelse($listaComentarios as $comentario)
                @php
                    $autorComentario = $comentario->user ?? $comentario->usuario ?? null;
                    $nomeAutor = $autorComentario->nome ?? $autorComentario->name ?? 'Usuário';
                    $rawAvatarAutor = $autorComentario->imagem ?? $autorComentario->avatar ?? null;
                    $avatarAutorUrl = null;
                    if (! empty($rawAvatarAutor)) {
                        $avatarAutorUrl = (str_starts_with($rawAvatarAutor, 'http://') || str_starts_with($rawAvatarAutor, 'https://'))
                            ? $rawAvatarAutor
                            : asset('storage/' . ltrim(str_replace('public/', '', $rawAvatarAutor), '/'));
                    }
                    $textoComentario = trim((string) ($comentario->conteudo ?? $comentario->mensagem ?? $comentario->texto ?? ''));
                @endphp

                <div class="w-full p-4 rounded-xl bg-gray-50 dark:bg-[#161f30] border border-gray-200/80 dark:border-[#273449] flex items-start gap-3.5 text-left">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-emerald-100 dark:bg-emerald-950/60 border border-gray-200 dark:border-[#273449] flex items-center justify-center shrink-0">
                        @if($avatarAutorUrl)
                            <img
                                src="{{ $avatarAutorUrl }}"
                                alt="Foto de {{ $nomeAutor }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <span class="material-symbols-outlined text-xl text-[#236350] dark:text-emerald-400 leading-none">person</span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 text-left flex flex-col gap-0.5">
                        <div class="flex items-baseline justify-between gap-3 w-full">
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100 leading-tight truncate">
                                {{ $nomeAutor }}
                            </span>
                            @if(!empty($comentario->created_at))
                                <span class="text-xs text-gray-400 dark:text-gray-500 leading-tight shrink-0">
                                    {{ \Carbon\Carbon::parse($comentario->created_at)->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>

                        <p style="margin: 0;" class="text-sm text-gray-700 dark:text-gray-300 leading-tight wrap-break-words text-left">
                            {{ $textoComentario }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 rounded-xl border border-dashed border-gray-200 dark:border-[#273449] text-sm text-gray-500 dark:text-gray-400">
                    Nenhum comentário ainda nesta oferta.
                </div>
            @endforelse
        </div>
    </section>
</div>

@push('scripts')
<script>
    window.addEventListener('load', function () {
        if (!window.Echo) {
            console.error('Echo não foi carregado.');
            return;
        }

        const ofertaShow = document.getElementById('oferta-show');

        if (!ofertaShow) {
            console.error('Elemento da oferta não encontrado.');
            return;
        }

        const ofertaId = Number(ofertaShow.dataset.ofertaId);

        window.Echo.channel('ofertas')
            .listen('.oferta.atualizada', function (event) {
                if (!event?.oferta || Number(event.oferta.id) !== ofertaId) {
                    return;
                }

                const valor = Number(event.oferta.valor);

                document.getElementById('oferta-valor').textContent =
                    valor.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                document.getElementById('oferta-valor-box').textContent =
                    valor.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                const quantidade = Number(event.oferta.quantidade);
                let quantidadeFormatada;

                if (event.oferta.unidade?.toLowerCase() === 'saca') {
                    quantidadeFormatada = quantidade.toLocaleString('pt-BR', {
                        maximumFractionDigits: 0
                    });
                } else {
                    quantidadeFormatada = quantidade.toLocaleString('pt-BR', {
                        minimumFractionDigits: quantidade % 1 === 0 ? 0 : 2,
                        maximumFractionDigits: 2
                    });
                }

                document.getElementById('oferta-quantidade').textContent =
                    quantidadeFormatada;

                document.getElementById('oferta-quantidade-unidade').textContent =
                    event.oferta.unidade ?? '';

                document.getElementById('oferta-unidade').textContent =
                    '/ ' + (event.oferta.unidade ?? 'Unidade');

                const statusElemento = document.getElementById('oferta-status');

                if (statusElemento && event.oferta.status) {
                    const status = String(event.oferta.status);

                    statusElemento.textContent =
                        status.charAt(0).toUpperCase() + status.slice(1);

                    statusElemento.className =
                        'inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold';

                    if (status === 'publicada') {
                        statusElemento.classList.add(
                            'text-green-700',
                            'dark:text-emerald-300',
                            'bg-green-50',
                            'dark:bg-emerald-950/60',
                            'border',
                            'border-green-100',
                            'dark:border-emerald-800/60'
                        );
                    } else if (status === 'encerrada') {
                        statusElemento.classList.add(
                            'text-yellow-700',
                            'dark:text-amber-300',
                            'bg-yellow-50',
                            'dark:bg-amber-950/60',
                            'border',
                            'border-yellow-100',
                            'dark:border-amber-800/60'
                        );
                    } else {
                        statusElemento.classList.add(
                            'text-red-700',
                            'dark:text-red-300',
                            'bg-red-50',
                            'dark:bg-red-950/60',
                            'border',
                            'border-red-100',
                            'dark:border-red-800/60'
                        );
                    }
                }
            })
            .listen('.oferta.excluida', function (event) {
                if (Number(event?.ofertaId) !== ofertaId) {
                    return;
                }

                ofertaShow.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <span class="material-symbols-outlined text-6xl text-gray-400 dark:text-gray-500 leading-none mb-4">
                            inventory_2
                        </span>

                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                            Oferta não disponível
                        </h2>

                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            Esta oferta foi excluída pelo fornecedor e não está mais disponível.
                        </p>

                        <a
                            href="{{ route('home') }}"
                            class="btn-copav inline-flex items-center justify-center gap-2 text-white px-5 py-2.5 rounded-lg transition-colors"
                        >
                            <span>Voltar para a página inicial</span>
                        </a>
                    </div>
                `;
            });
    });
</script>
@endpush

@endsection
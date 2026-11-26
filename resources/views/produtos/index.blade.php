@extends('layouts.layout')
@section('title', 'Produtos cadastrados')

@section('conteudo')

@php
    $usuarioLogado = auth()->user();
    $fornecedorDoUsuario = $usuarioLogado?->fornecedor;
    $isAdmin = $usuarioLogado !== null && $usuarioLogado->isAdmin();
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

<div class="w-full max-w-7xl mx-auto px-2 sm:px-4">

    {{-- Cabeçalho --}}
    <div class="flex flex-row items-center justify-between gap-4 mb-6 text-left border-b border-gray-200 dark:border-[#273449] pb-4">

        <div class="text-left">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Produtos
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Produtos cadastrados no marketplace
            </p>
        </div>

        @auth
            <a
                href="{{ route('produtos.create') }}"
                class="btn-copav inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors shadow-sm shrink-0">
                <span class="material-symbols-outlined text-xl leading-none">
                    add
                </span>
                <span>Cadastrar produto</span>
            </a>
        @endauth

    </div>

    {{-- Mensagem de sucesso --}}
    @if(session('sucesso'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-emerald-950/50 border-l-4 border-green-600 dark:border-emerald-400 text-green-700 dark:text-emerald-200 rounded-r-md text-left">
            {{ session('sucesso') }}
        </div>
    @endif

    {{-- Lista de produtos --}}
    @if($produtos->count())

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">

            @foreach($produtos as $produto)
                @php
                    $imagemProduto = $produto->imagem
                        ? (str_contains($produto->imagem, 'assets/')
                            ? asset($produto->imagem)
                            : asset('storage/' . ltrim(str_replace('public/', '', $produto->imagem), '/')))
                        : asset('assets/milho.png');

                    $podeEditarProduto = $usuarioLogado !== null && (
                        $isAdmin ||
                        ($fornecedorDoUsuario && (int) $fornecedorDoUsuario->id === (int) ($produto->fornecedor_id ?? 0))
                    );
                @endphp

                <div class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] overflow-hidden hover:shadow-lg transition-all text-left">

                    {{-- Imagem --}}
                    <div class="w-full h-48 bg-gray-100 dark:bg-slate-800 overflow-hidden">
                        <img
                            src="{{ $imagemProduto }}"
                            alt="{{ $produto->nome ?? 'Imagem do produto' }}"
                            class="w-full h-full object-cover"
                            onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                        />
                    </div>

                    {{-- Conteúdo --}}
                    <div class="p-4">

                        {{-- Categoria --}}
                        <span class="inline-block text-xs font-semibold text-[#236350] dark:text-emerald-300 bg-green-50 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60 px-2.5 py-1 rounded-full mb-2">
                            {{ $produto->categoria?->nome ?? 'Sem categoria' }}
                        </span>

                        {{-- Nome --}}
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 line-clamp-1">
                            {{ $produto->nome ?? 'Produto sem nome' }}
                        </h2>

                        {{-- Descrição --}}
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2 min-h-40px">
                            {{ $produto->descricao ?? 'Nenhuma descrição cadastrada.' }}
                        </p>

                        {{-- Informações --}}
                        <div class="mt-4 space-y-2 text-sm">

                            <div class="flex items-center justify-between">
                                <span class="text-gray-500 dark:text-gray-400">
                                    Unidade
                                </span>

                                <span class="font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $produto->unidade ?? '—' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-2">
                                <span class="text-gray-500 dark:text-gray-400 shrink-0">
                                    Fornecedor
                                </span>

                                <span class="font-semibold text-gray-700 dark:text-gray-200 truncate max-w-150px">
                                    {{ $produto->fornecedor?->nome ?? 'Não informado' }}
                                </span>
                            </div>

                        </div>

                        {{-- Botões --}}
                        <div class="flex items-center gap-2 mt-5 pt-4 border-t border-gray-100 dark:border-[#273449]">

                            <a
                                href="{{ route('produtos.show', $produto) }}"
                                class="btn-copav flex-1 inline-flex items-center justify-center text-center text-white px-3 py-2 rounded-lg text-sm font-semibold transition-opacity hover:opacity-95"
                            >
                                <span>Ver detalhes</span>
                            </a>

                            @if($podeEditarProduto)
                                <a
                                    href="{{ route('produtos.edit', $produto) }}"
                                    class="inline-flex items-center justify-center w-10 h-10 border border-gray-300 dark:border-[#273449] text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white rounded-lg transition-colors shrink-0"
                                    title="Editar"
                                    aria-label="Editar produto"
                                >
                                    <span class="material-symbols-outlined text-lg leading-none">
                                        settings
                                    </span>
                                </a>
                            @endif

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        {{-- Nenhum produto --}}
        <div class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] p-10 text-center">

            <span class="material-symbols-outlined text-6xl text-gray-300 dark:text-gray-600 leading-none">
                inventory_2
            </span>

            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-100 mt-4">
                Nenhum produto cadastrado
            </h2>

            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                Ainda não existem produtos disponíveis no marketplace.
            </p>

            @auth
                <a
                    href="{{ route('produtos.create') }}"
                    class="btn-copav inline-flex items-center justify-center gap-2 mt-5 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-opacity hover:opacity-95"
                >
                    <span class="material-symbols-outlined text-lg leading-none">
                        add
                    </span>

                    <span>Cadastrar primeiro produto</span>
                </a>
            @endauth

        </div>

    @endif

</div>

@endsection
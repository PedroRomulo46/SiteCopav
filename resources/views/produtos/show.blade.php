@extends('layouts.layout')

@section('title', $produto->nome ?? 'Produto')

@section('conteudo')

@php
    $usuarioLogado = auth()->user();
    $fornecedorDoUsuario = $usuarioLogado?->fornecedor;
    $isAdmin = $usuarioLogado !== null && $usuarioLogado->isAdmin();

    $podeEditarProduto = $usuarioLogado !== null && (
        $isAdmin ||
        ($fornecedorDoUsuario && (int) $fornecedorDoUsuario->id === (int) ($produto->fornecedor_id ?? 0))
    );

    $imagemProduto = $produto->imagem
        ? (str_contains($produto->imagem, 'assets/')
            ? asset($produto->imagem)
            : asset('storage/' . ltrim(str_replace('public/', '', $produto->imagem), '/')))
        : asset('assets/milho.png');
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

<div class="max-w-5xl mx-auto my-6 text-left px-2 sm:px-4">
    <div class="bg-white dark:bg-[#111827] rounded-xl shadow-md border border-gray-200 dark:border-[#273449] overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- Imagem --}}
            <div class="bg-gray-100 dark:bg-slate-800 min-h-350px overflow-hidden">
                <img
                    src="{{ $imagemProduto }}"
                    alt="{{ $produto->nome ?? 'Imagem do produto' }}"
                    class="w-full h-full min-h-350px object-cover"
                    onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                />
            </div>

            {{-- Informações --}}
            <div class="p-6 md:p-8 flex flex-col">

                {{-- Categoria --}}
                <div class="mb-3">
                    <span class="inline-block text-xs font-semibold text-[#236350] dark:text-emerald-300 bg-green-50 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60 px-3 py-1 rounded-full">
                        {{ $produto->categoria?->nome ?? 'Sem categoria' }}
                    </span>
                </div>

                {{-- Nome --}}
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $produto->nome ?? 'Produto sem nome' }}
                </h1>

                {{-- Descrição --}}
                <div class="mt-5">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase">
                        Descrição
                    </h2>
                    <p class="text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                        {{ $produto->descricao ?? 'Nenhuma descrição cadastrada.' }}
                    </p>
                </div>

                {{-- Informações --}}
                <div class="mt-6 border-t border-gray-100 dark:border-[#273449] pt-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">
                            Unidade
                        </span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">
                            {{ $produto->unidade ?? '—' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center gap-2">
                        <span class="text-gray-500 dark:text-gray-400">
                            Fornecedor
                        </span>
                        <span class="font-semibold text-gray-800 dark:text-gray-100 text-right">
                            {{ $produto->fornecedor?->nome ?? 'Não informado' }}
                        </span>
                    </div>
                </div>

                {{-- Botões --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-auto pt-8">

                    @if($podeEditarProduto)
                        <a
                            href="{{ route('produtos.edit', $produto) }}"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 text-center border border-gray-300 dark:border-[#273449] text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-lg leading-none">edit</span>
                            <span>Editar produto</span>
                        </a>
                    @endif

                    <a
                        href="{{ route('home') }}"
                        class="btn-copav flex-1 inline-flex items-center justify-center gap-1.5 text-center text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-opacity hover:opacity-95">
                        <span class="material-symbols-outlined text-lg leading-none">arrow_back</span>
                        <span>Voltar para o início</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Ofertas deste produto --}}
    @if($produto->ofertas && $produto->ofertas->count())

        <div class="mt-6">

            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                Ofertas deste produto
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($produto->ofertas as $oferta)

                    <a
                        href="{{ route('ofertas.show', $oferta) }}"
                        class="bg-white dark:bg-[#111827] rounded-xl border border-gray-200 dark:border-[#273449] hover:border-gray-300 dark:hover:border-slate-600 shadow-sm p-4 hover:shadow-md transition-all block">

                        <div class="flex justify-between items-start gap-3">

                            <div>
                                <p class="font-bold text-gray-800 dark:text-gray-100">
                                    {{ $oferta->quantidade }} {{ $oferta->unidade ?? ($produto->unidade ?? '') }}
                                </p>

                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $oferta->localizacao ?? 'Localização não informada' }}
                                </p>
                            </div>

                            <span class="font-bold text-[#236350] dark:text-emerald-400 shrink-0">
                                R$ {{ number_format((float) ($oferta->valor ?? 0), 2, ',', '.') }}
                            </span>
                        </div>

                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            Status:
                            <span class="font-semibold text-gray-700 dark:text-gray-200">
                                {{ ucfirst((string) ($oferta->status ?? 'indefinido')) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
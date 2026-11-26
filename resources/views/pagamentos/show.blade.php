@extends('layouts.layout')

@section('title', 'Pagamento')

@section('conteudo')

@php
    $produto = $oferta?->produto;

    $imagemProduto = $produto && $produto->imagem
        ? (
            str_contains($produto->imagem, 'assets/')
                ? asset($produto->imagem)
                : asset('storage/' . ltrim(
                    str_replace('public/', '', $produto->imagem),
                    '/'
                ))
        )
        : asset('assets/milho.png');
@endphp

{{-- Breadcrumb --}}
<nav class="flex flex-wrap items-center gap-2 text-sm mb-2 text-gray-600 dark:text-gray-400"
     aria-label="Breadcrumb">

    <a href="{{ route('home') }}"
       class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
        Início
    </a>

    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
        chevron_right
    </span>

    <a href="{{ route('negociacoes.compras') }}"
       class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-[#236350] dark:hover:text-emerald-400 transition-colors">
        Minhas Compras
    </a>

    <span class="material-symbols-outlined text-gray-400 dark:text-gray-500 text-lg leading-none">
        chevron_right
    </span>

    <span class="font-semibold text-gray-800 dark:text-gray-100 leading-none">
        Verificar Pagamento
    </span>
</nav>

<div class="min-h-screen text-left">
    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

        {{-- Cabeçalho --}}
        <header class="mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold
                       tracking-tight text-gray-900 dark:text-gray-100">
                Verificar Pagamento
            </h1>

            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm sm:text-base">
                Confira os dados da proposta aceita antes de continuar.
            </p>
        </header>

        {{-- Card do produto --}}
        <section class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449] shadow-sm p-4 sm:p-5 lg:p-6 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-[260px_minmax(0,1fr)] gap-6 lg:gap-8 items-center">

                {{-- Imagem do produto --}}
                <div class="relative flex justify-center w-full">
                    <img
                        src="{{ $imagemProduto }}"
                        alt="{{ $produto->nome ?? 'Produto' }}"
                        class="w-full h-56 md:h-64 object-cover rounded-xl bg-gray-100 dark:bg-slate-800 shadow-sm"
                        onerror="this.onerror=null; this.src='{{ asset('assets/milho.png') }}';"
                    >
                </div>

                {{-- Informações do produto --}}
                <div class="min-w-0">
                    <p class="flex items-center gap-2 text-xs font-bold
                            uppercase tracking-wider text-[#236350] dark:text-emerald-400 mb-2">
                        <span class="material-symbols-outlined text-lg leading-none">
                            eco
                        </span>
                        <span>Produto</span>
                    </p>

                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100
                            wrap-break-words">
                        {{ $produto->nome ?? 'Produto' }}
                    </h2>

                    {{-- Vendedor --}}
                    <div class="flex items-center gap-3 mt-4">
                        <div class="w-10 h-10 shrink-0 rounded-xl
                                    bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-700 dark:text-gray-300 leading-none">
                                store
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Vendedor</p>

                            <span class="font-bold text-gray-900 dark:text-gray-100 wrap-break-words">
                                {{ $oferta?->fornecedor?->nome ?? 'Fornecedor' }}
                            </span>
                        </div>
                    </div>

                    {{-- Negociação e status --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                        <div class="rounded-xl border border-gray-200 dark:border-[#273449]
                                    bg-gray-50 dark:bg-[#161f30] p-3 flex items-center gap-3">
                            <span class="material-symbols-outlined
                                        text-[#236350] dark:text-emerald-400 text-xl leading-none shrink-0">
                                verified
                            </span>

                            <div class="min-w-0">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                                <p class="font-semibold text-emerald-700 dark:text-emerald-400">
                                    Proposta aceita
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- Resumo e painel lateral --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- Resumo do pedido --}}
            <section class="lg:col-span-2 bg-white dark:bg-[#111827] rounded-2xl
                            border border-gray-200 dark:border-[#273449] shadow-sm overflow-hidden">

                <div class="flex items-center gap-3 px-5 sm:px-6 py-5
                            border-b border-gray-200 dark:border-[#273449]">
                    <span class="material-symbols-outlined
                                 text-[#236350] dark:text-emerald-400 text-2xl leading-none">
                        description
                    </span>

                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-gray-100">
                        Resumo do pedido
                    </h2>
                </div>

                <div class="p-5 sm:p-6">

                    {{-- Valor unitário --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 shrink-0 rounded-full
                                    bg-emerald-50 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60 flex items-center justify-center">
                            <span class="material-symbols-outlined
                                         text-[#236350] dark:text-emerald-400 text-2xl leading-none">
                                payments
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Valor por unidade
                            </p>

                            <p class="flex flex-wrap items-baseline gap-2">
                                <span class="text-2xl font-bold text-[#236350] dark:text-emerald-400">
                                    R$ {{ number_format((float) ($valorUnitario ?? 0), 2, ',', '.') }}
                                </span>

                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    / {{ $oferta?->unidade ?? 'Unidade' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Quantidade --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 shrink-0 rounded-full
                                    bg-emerald-50 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60 flex items-center justify-center">
                            <span class="material-symbols-outlined
                                         text-[#236350] dark:text-emerald-400 text-2xl leading-none">
                                scale
                            </span>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Quantidade aceita
                            </p>

                            <p class="flex flex-wrap items-baseline gap-2">
                                <span class="text-2xl font-bold text-[#236350] dark:text-emerald-400">
                                    {{ number_format((float) ($quantidade ?? 0), 2, ',', '.') }}
                                </span>

                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $oferta?->unidade ?? '' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="border-t border-gray-200 dark:border-[#273449] pt-5">
                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/50
                                    border border-emerald-100 dark:border-emerald-800/60 p-4 sm:p-5
                                    flex items-center gap-4">

                            <div class="w-12 h-12 shrink-0 rounded-full
                                        bg-white dark:bg-[#111827] border border-emerald-100 dark:border-emerald-800/60
                                        flex items-center justify-center">
                                <span class="material-symbols-outlined
                                             text-[#236350] dark:text-emerald-400 text-2xl leading-none">
                                    account_balance_wallet
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">
                                    Total do pedido
                                </p>

                                <p class="text-2xl sm:text-3xl font-extrabold
                                          text-[#236350] dark:text-emerald-400 wrap-break-words">
                                    R$ {{ number_format((float) ($valorTotal ?? 0), 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Forma de pagamento --}}
                    <div class="mt-5 rounded-xl border border-gray-200 dark:border-[#273449]
                                bg-gray-50 dark:bg-[#161f30] p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 shrink-0 rounded-full
                                        bg-emerald-100 dark:bg-emerald-950/60 border border-transparent dark:border-emerald-800/60 flex items-center justify-center">
                                <span class="material-symbols-outlined
                                             text-[#236350] dark:text-emerald-400 text-xl leading-none">
                                    credit_card
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-gray-100">
                                    Forma de pagamento
                                </h3>

                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                                    As opções de pagamento estarão disponíveis
                                    durante a operação de pagamento.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            {{-- Painel lateral --}}
            <aside class="bg-white dark:bg-[#111827] rounded-2xl border border-gray-200 dark:border-[#273449]
                          shadow-sm p-5">

                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border
                            border-emerald-100 dark:border-emerald-800/60 p-4 mb-3">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined
                                     text-[#236350] dark:text-emerald-400 text-2xl leading-none shrink-0">
                            verified_user
                        </span>

                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-gray-100">
                                Compra segura
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 leading-relaxed">
                                Confira os dados da negociação antes de
                                prosseguir com a compra.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 py-4 border-b border-gray-100 dark:border-[#273449]">
                    <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none shrink-0 mt-0.5">
                        lock
                    </span>

                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                            Proteção de dados
                        </h4>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Mantenha seus dados de acesso protegidos.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 py-4 border-b border-gray-100 dark:border-[#273449]">
                    <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none shrink-0 mt-0.5">
                        handshake
                    </span>

                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                            Negociação registrada
                        </h4>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            A proposta foi aceita pelo vendedor.
                        </p>
                    </div>
                </div>

                <div class="flex items-start gap-3 pt-4">
                    <span class="material-symbols-outlined text-[#236350] dark:text-emerald-400 text-xl leading-none shrink-0 mt-0.5">
                        support_agent
                    </span>

                    <div>
                        <h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                            Acompanhe sua compra
                        </h4>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Consulte o chat para acompanhar a negociação.
                        </p>
                    </div>
                </div>
            </aside>
        </div>

        {{-- Ações --}}
        <div class="mt-6 flex flex-col-reverse sm:flex-row gap-3
                    sm:justify-between">

            <a
                href="{{ route('negociacoes.compras') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl
                       border border-gray-300 dark:border-[#273449] bg-white dark:bg-[#111827] px-5 py-3
                       text-sm font-semibold text-gray-700 dark:text-gray-200
                       hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors"
            >
                <span class="material-symbols-outlined text-lg leading-none">
                    arrow_back
                </span>
                <span>Voltar à página de compras</span>
            </a>

            {{-- Área com Alpine.js --}}
            <div x-data="{ modalAberto: false }">

                {{-- Botão que abre o Modal --}}
                <button @click="modalAberto = true" type="button" class="btn-copav inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white transition-opacity hover:opacity-95 w-full shadow-sm cursor-pointer">
                    <span class="material-symbols-outlined text-lg leading-none">paid</span>
                    <span>Efetuar pagamento</span>
                </button>

                {{-- MODAL DE CONFIRMAÇÃO --}}
                <div x-show="modalAberto" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="modalAberto = false" class="bg-white dark:bg-[#111827] border border-gray-200 dark:border-[#273449] rounded-2xl shadow-xl max-w-md w-full p-6 text-center transform transition-all">

                        <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-950/60 text-[#236350] dark:text-emerald-400 border border-transparent dark:border-emerald-800/60 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-2xl leading-none">lock</span>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Confirmar Pagamento</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Você está prestes a efetuar o pagamento simulado desta negociação. Deseja prosseguir com a transação?
                        </p>

                        <form action="{{ route('pagamentos.confirmar', $negociacao) }}" method="POST" class="flex gap-3">
                            @csrf
                            <button @click="modalAberto = false" type="button" class="flex-1 rounded-xl border border-gray-300 dark:border-[#273449] bg-white dark:bg-[#161f30] py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-slate-800 hover:text-gray-900 dark:hover:text-white transition-colors cursor-pointer">
                                Cancelar
                            </button>

                            <button type="submit" class="btn-copav flex-1 rounded-xl py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-95 shadow-sm cursor-pointer">
                                Confirmar
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

@endsection